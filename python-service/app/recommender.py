def recommend_topics(topics, limit=5):
    seen=[]
    for t in topics:
        if t and t not in seen:
            seen.append(t)
    return seen[:max(1, min(int(limit), 20))]
