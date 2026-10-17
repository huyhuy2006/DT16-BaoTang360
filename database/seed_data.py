from pathlib import Path
import csv, json

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / 'database' / 'raw' / 'dt16_300_records.csv'
MANIFEST = ROOT / 'database' / 'dataset_manifest.json'

TYPES = ['space','scene','artifact','hotspot','narration','tour','tour_step','guestbook','collection_item','visit_log']
DESTINATIONS = ['Hà Nội','Huế','Đà Nẵng','Hội An','Quảng Ninh']
TOPICS = ['di sản','gốm','dân tộc học','lịch sử','nghệ thuật']
SOURCE_NOTE = 'Dữ liệu mô phỏng có căn cứ cho ĐT-16/CSE703073; thay bằng nguồn dữ liệu thật và ghi điều kiện sử dụng trước khi nộp.'


def generate(count=300):
    if count != 300:
        raise ValueError('Bộ dataset ĐT-16 này được chuẩn hóa đúng 300 bản ghi.')
    OUT.parent.mkdir(parents=True, exist_ok=True)
    rows = []
    for i in range(1, count + 1):
        record_type = TYPES[(i - 1) % len(TYPES)]
        rows.append({
            'record_id': i,
            'record_type': record_type,
            'name': f'ĐT-16 {record_type} {i:03d}',
            'destination': DESTINATIONS[(i - 1) % len(DESTINATIONS)],
            'topic': TOPICS[(i - 1) % len(TOPICS)],
            'source_note': SOURCE_NOTE,
        })
    with OUT.open('w', newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=rows[0].keys())
        writer.writeheader()
        writer.writerows(rows)
    counts = {t: sum(1 for r in rows if r['record_type'] == t) for t in TYPES}
    manifest = {
        'dataset_name': 'DT16 virtual museum tourism demo dataset',
        'minimum_required': 300,
        'records_in_package': 300,
        'source_type': 'Mô phỏng có căn cứ',
        'source_note': SOURCE_NOTE,
        'record_type_counts': counts,
    }
    MANIFEST.write_text(json.dumps(manifest, ensure_ascii=False, indent=2), encoding='utf-8')
    return OUT


if __name__ == '__main__':
    print(generate())
