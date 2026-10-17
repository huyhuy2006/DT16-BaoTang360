# DT-16 — Bảo tàng ảo và tour tham quan 360° trên nền web

Bộ mã nguồn học tập cho CSE703073, xây dựng trên Laravel 11 + MySQL, Vue 3/Vite và một dịch vụ Python/FastAPI. Source package có dữ liệu demo, panorama/media demo, migrations, tài liệu thiết kế và ca kiểm thử.

> **Trung thực về dữ liệu:** `database/raw/dt16_300_records.csv` có đúng 300 dòng dữ liệu mô phỏng (không tính tiêu đề), mỗi loại 30 bản ghi: space, scene, artifact, hotspot, narration, tour, tour_step, guestbook, collection_item, visit_log. Đây không phải 300 hồ sơ bảo tàng đã được xác minh từ nguồn chính thức. Thay thế hoặc bổ sung dữ liệu thật có giấy phép trước khi nộp.
>
> **Trạng thái kiểm thử:** gói có 20 định nghĩa test PHPUnit và 40 định nghĩa Playwright (60 test definitions) cùng một ma trận 60 testcase. Trạng thái trong ma trận mặc định là **Chưa chạy**; chưa có gì được đánh dấu PASS thay người dùng. Hãy chạy trên máy local và cập nhật bằng chứng thật.
>
> **Tài khoản demo local/test:** `admin@example.test`, `editor@example.test`, `visitor1@example.test`... Mật khẩu cố định trong seeder: `MatKhau@123`. Seeder chỉ được gọi tự động khi `APP_ENV=local` hoặc `testing`; entrypoint bỏ qua seeder khi `APP_ENV=production`. Không dùng mật khẩu demo trên hệ thống công khai; tạo tài khoản quản trị riêng với mật khẩu mạnh.

Yêu cầu đầu vào và đối chiếu: `docs/REQUIREMENTS_DT16.md`, `docs/FEATURE_TRACEABILITY.csv`, `docs/NON_FUNCTIONAL_CHECKLIST.md`.

## Tính năng được hiện thực trong source

- Đăng ký, đăng nhập, đăng xuất, quên/đặt lại mật khẩu, đổi mật khẩu và chỉnh sửa hồ sơ.
- Vai trò `admin`, `editor`, `visitor`; quản trị viên đổi vai trò và khóa/mở khóa tài khoản; tài khoản khóa không thể đăng nhập.
- Danh sách/tìm kiếm/lọc theo tên và chi tiết hiện vật; ảnh, URL video tùy chọn và thuyết minh đa ngữ có audio/transcript.
- Không gian bảo tàng, các scene/điểm nhìn, panorama 360°, điểm nóng (hotspot), liên kết điều hướng giữa các scene; tour có thứ tự và chế độ có dẫn dắt/tự do.
- Lưu tiến trình tour theo tài khoản (điểm đang xem, scene đã đi qua, đánh dấu hoàn tất).
- Lưu hiện vật yêu thích qua bộ sưu tập cũ, và lưu yêu thích tổng quát cho hiện vật/tour.
- Đánh giá 1–5 sao, phản hồi có kiểm duyệt trước khi hiển thị công khai; màn hình admin duyệt/từ chối.
- Thống kê visit logs; FastAPI phục vụ ETL, tóm tắt analytics, gợi ý chủ đề; Laravel có timeout và phương án fallback cho gợi ý.
- Hai chủ đề giao diện, chuyển ngôn ngữ, bố cục responsive, kiểm tra trạng thái `/healthz`.

Media ở `backend/storage/app/public/` là media demo đi kèm gói. Kiểm tra quyền sử dụng và thay nội dung mô phỏng/placeholder bằng tài nguyên có giấy phép trước khi triển khai công khai. Panorama viewer nạp Pannellum từ CDN trong `frontend/index.html`, do đó cần Internet khi dùng cách khởi chạy mặc định.

## Điều kiện để chạy

- Windows 10/11 + Windows PowerShell 5.1 hoặc PowerShell 7.
- Git (tùy chọn để quản lý mã nguồn), Docker Desktop và Docker Compose plugin (khuyến nghị để chạy cả bốn dịch vụ).
- Nếu chạy thủ công: PHP 8.2+, Composer, Node.js 20+, Python 3.11+, MySQL 8.0+.

## Cách nhanh trên Windows bằng Docker Desktop

Mở Windows PowerShell tại thư mục vừa giải nén, ví dụ:

```powershell
Set-Location "C:\CSE703073\BaoTang360"

# Tạo .env local với khóa/mật khẩu ngẫu nhiên; script không ghi đè .env đã có.
Set-ExecutionPolicy -Scope Process -ExecutionPolicy RemoteSigned
.\tools\Initialize-DT16.ps1

# Xác minh .env có tồn tại; KHÔNG in nội dung file ra terminal.
Test-Path .\.env

# Khởi chạy dịch vụ theo docker-compose.yml.
docker compose up -d --build

docker compose ps

# Kiểm tra Laravel + MySQL + Python.
Invoke-RestMethod "http://127.0.0.1:8080/healthz"
Invoke-RestMethod "http://127.0.0.1:8001/healthz"
```

Mở giao diện: `http://127.0.0.1:5173`.

Lần khởi động đầu sẽ tải image và cài phụ thuộc, vì vậy có thể mất thời gian. Nếu Docker báo lỗi, lưu `docker compose ps` và `docker compose logs --tail 100 backend nginx mysql python-service frontend` để chẩn đoán. Không xóa volume để chữa lỗi vì volume có thể chứa dữ liệu local.

## Cách chạy thủ công (không dùng Docker)

Cách này yêu cầu MySQL đang chạy và bạn đã tạo CSDL/tài khoản. Không chạy `migrate:fresh` trên CSDL có dữ liệu cần giữ.

Terminal PowerShell 1 — Laravel:

```powershell
Set-Location "C:\CSE703073\BaoTang360\backend"
composer install
Copy-Item .env.example .env -ErrorAction Stop
php artisan key:generate
# Chỉnh DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD trong backend\.env
# bằng cấu hình CSDL local của bạn trước khi chạy dòng tiếp theo.
php artisan migrate
php artisan db:seed --class=DemoDataSeeder
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8000
```

Terminal PowerShell 2 — Vue:

```powershell
Set-Location "C:\CSE703073\BaoTang360\frontend"
npm ci
# VITE_API_BASE_URL mặc định trong .env.example trỏ tới Docker cổng 8080.
# Với php artisan serve, dùng VITE_API_BASE_URL=http://127.0.0.1:8000/api
# và VITE_STORAGE_BASE_URL=http://127.0.0.1:8000/storage trong .env.local.
npm run dev -- --host 127.0.0.1
```

Terminal PowerShell 3 — Python:

```powershell
Set-Location "C:\CSE703073\BaoTang360\python-service"
py -3 -m venv .venv
.\.venv\Scripts\Activate.ps1
python -m pip install -r requirements.txt
$env:PYTHON_SERVICE_TOKEN = "THAY_BANG_TOKEN_LOCAL_CUA_BAN"
uvicorn app.main:app --host 127.0.0.1 --port 8001
```

Nếu chạy Python riêng, đặt `PYTHON_SERVICE_URL=http://127.0.0.1:8001` và cùng giá trị `PYTHON_SERVICE_TOKEN` ở cấu hình Laravel local; không đưa token thật vào Git.

## Bộ dữ liệu 300 dòng

- Nguồn: `database/raw/dt16_300_records.csv` — đúng 300 dữ liệu, UTF-8 không BOM.
- Manifest loại bản ghi: `database/dataset_manifest.json`.
- Script tạo lại CSV: `database/seed_data.py` (chỉ sinh dữ liệu mô phỏng, không truy cập nguồn bên ngoài).
- Seeder Laravel tạo dữ liệu ứng dụng quan hệ và số lượng bản ghi tổng thể vượt 300; chỉ chạy seed trên CSDL local/test sạch hoặc sau khi đã sao lưu.

## Chạy kiểm tra

```powershell
# Chạy từ thư mục gốc dự án.
Set-ExecutionPolicy -Scope Process -ExecutionPolicy RemoteSigned
.\tools\verify_dt16.ps1

# Laravel tests (khi Composer dependencies đã cài).
Set-Location .\backend
php artisan test

# Vue unit/build (khi npm dependencies đã cài).
Set-Location ..\frontend
npm ci
npm run build

# E2E 3 engine trình duyệt (các dịch vụ Laravel/Python phải đang chạy).
# Playwright được cài trong frontend/package.json nên chạy từ thư mục frontend.
npx playwright install chromium firefox webkit
npm run test:e2e
```

`05_KiemThu/60-ca-kiem-thu.csv` là kế hoạch 60 testcase với kết quả thực tế để trống và trạng thái “Chưa chạy”. Không ghi PASS cho đến khi bạn thực thi và lưu minh chứng.

## Cây thư mục gốc

```text
.
├── backend/                         # Laravel API, models, requests, migrations, seeders, tests
├── frontend/                        # Vue 3/Vite, views/components/stores/router + Playwright config/tests
├── python-service/                  # FastAPI: ETL, analytics, recommendation, healthz
├── database/                        # CSV 300 dòng, schema SQL, manifest, ERD
├── 05_KiemThu/                      # Ma trận test 60 ca và tài liệu kiểm thử
├── docs/                            # Yêu cầu, truy vết tính năng, NFR, kiến trúc, OpenAPI, wireframes
├── docker/                          # PHP entrypoint/Dockerfile, Nginx config
├── tools/                           # PowerShell kiểm tra và khởi tạo môi trường
├── docker-compose.yml
├── .env.example                     # Mẫu biến môi trường, không phải mật khẩu thật
└── README.md
```

## Lưu ý phát hành

- Source ZIP không chứa lịch sử `.git`; không giả lập commit lịch sử. Đưa mã vào kho nhóm thực, rà soát `git status`, chạy test, sau đó mới tạo tag `v1.0.0` theo quy trình nhóm.
- Không đưa `.env`, `vendor`, `node_modules`, `.venv`, log, dump cơ sở dữ liệu, credentials hoặc video dung lượng lớn vào ZIP nộp.
- Không công khai Laravel ở `APP_DEBUG=true`; tạo biến môi trường riêng cho production và thông tin bí mật riêng cho từng môi trường.
