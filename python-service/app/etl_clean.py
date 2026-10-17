def normalize_records(rows):
    normalized = []
    for row in rows:
        item = dict(row)
        if "name" in item and isinstance(item["name"], str):
            item["name"] = " ".join(item["name"].split())
        normalized.append(item)
    return normalized
