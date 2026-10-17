import os
from fastapi import FastAPI, Header, HTTPException
from .analytics import summarize_actions
from .etl_clean import normalize_records
from .recommender import recommend_topics

app = FastAPI(title="DT16 Python Service", version="1.1.0")
TOKEN = os.getenv("PYTHON_SERVICE_TOKEN", "")

def auth(token: str | None):
    # Empty token is allowed only for local development; production should set a token.
    if TOKEN and token != TOKEN:
        raise HTTPException(status_code=401, detail="Invalid service token")

@app.get("/health")
@app.get("/healthz")
def health():
    # Public health response intentionally never returns the token value.
    return {"status": "ok", "service": "dt16-python"}

@app.post("/etl/normalize")
def normalize(payload: dict, x_service_token: str | None = Header(default=None)):
    auth(x_service_token)
    rows = payload.get("rows", [])
    return {"count": len(rows), "rows": normalize_records(rows)}

@app.post("/analytics/summary")
def analytics(payload: dict, x_service_token: str | None = Header(default=None)):
    auth(x_service_token)
    return summarize_actions(payload.get("actions", []))

@app.post("/recommendations")
def recommendations(payload: dict, x_service_token: str | None = Header(default=None)):
    auth(x_service_token)
    return {"recommendations": recommend_topics(payload.get("topics", []), payload.get("limit", 5))}
