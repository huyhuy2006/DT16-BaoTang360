from collections import Counter

def summarize_actions(actions):
    # `actions` đến từ VisitAnalyticsService.php: danh sách dict
    # {scene_id, hotspot_id, action, duration_seconds}, không phải danh sách chuỗi.
    # Phải trích trường "action" trước khi đếm, vì dict không hashable với Counter.
    labels = [
        a.get("action") if isinstance(a, dict) else a
        for a in actions
        if (a.get("action") if isinstance(a, dict) else a)
    ]
    counter = Counter(labels)
    durations = [
        a.get("duration_seconds")
        for a in actions
        if isinstance(a, dict) and isinstance(a.get("duration_seconds"), (int, float))
    ]
    avg_duration = round(sum(durations) / len(durations), 2) if durations else 0
    return {
        "total": len(actions),
        "by_action": dict(counter),
        "avg_duration_seconds": avg_duration,
    }
