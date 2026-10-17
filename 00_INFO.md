# 00_INFO — Bài tập lớn CSE703073 — ĐT-16

> Đây là mẫu thông tin bàn giao. Điền các trường đúng theo nhóm, URL và kết quả thật trước khi nộp.

## 1. Thông tin nhóm
- Lớp học phần: [điền]
- Tên nhóm: [điền]
- Mã đề tài: DT-16
- Tên đề tài: Bảo tàng ảo và tour tham quan 360° trên nền web

## 2. Danh sách thành viên và vai trò
| STT | Họ tên | MSSV | Vai trò | Tỷ lệ đóng góp |
|---|---|---|---|---|
| 1 | [điền] | [điền] | [điền] | [điền] |

## 3. Đường dẫn hệ thống triển khai
- URL HTTPS: [điền sau khi triển khai và kiểm tra]
- Thời gian cam kết duy trì: [ngày bắt đầu] đến [ngày kết thúc]
- Ghi chú về nền tảng/giới hạn vận hành: [điền trung thực]

## 4. Kho mã nguồn Git
- URL repository: [điền]
- Tag phát hành: `v1.0.0` (chỉ ghi sau khi tag đã được tạo và xác minh)
- Commit tag: [điền]
- Tổng commit/số tuần có commit: [lấy từ Git thật]

## 5. Video minh chứng
- URL công khai cho người có liên kết: [điền]
- Thời lượng video 8–12 phút: [điền]
- Đã thử mở ở cửa sổ ẩn danh: [điền kết quả thật]

## 6. Tài khoản kiểm thử
Chỉ điền tài khoản demo/test được tạo riêng. Không điền mật khẩu cá nhân, token, khóa API hoặc mật khẩu production.

| Vai trò | Tài khoản demo | Phạm vi quyền | Đã thử đăng nhập |
|---|---|---|---|
| Admin | [điền/đổi mật khẩu demo] | Quản trị | [chưa chạy] |
| Editor | [điền/đổi mật khẩu demo] | Quản lý nội dung | [chưa chạy] |
| Visitor | [điền/đổi mật khẩu demo] | Tham quan, đánh giá, yêu thích | [chưa chạy] |

## 7. Ngăn xếp công nghệ
- Backend: PHP 8.3 / Laravel 11 / Sanctum
- Database: MySQL 8
- Frontend: Vue 3 / Vite / Pinia / Vue Router
- Python: FastAPI (ETL, analytics, recommendations)
- Panorama: Pannellum + media demo trong `backend/storage/app/public`

## 8. Hướng dẫn khởi chạy lại
- Xem `README.md`.
- Docker Desktop: chạy `tools/Initialize-DT16.ps1`, sau đó `docker compose up -d --build`.
- Máy sạch đã thử nghiệm: [điền OS và ngày sau khi thực sự chạy]

## 9. Chức năng hoàn thành/chưa hoàn thành và giới hạn
- Mã nguồn dự kiến bao gồm 22 nhóm chức năng trong `docs/FEATURE_TRACEABILITY.csv`.
- Test definitions: 20 PHPUnit + 40 Playwright; 60 testcase thủ công ở `05_KiemThu/60-ca-kiem-thu.csv` mặc định `Chưa chạy`.
- Trạng thái kiểm thử đã thực thi: [cập nhật số liệu thật]
- Dữ liệu và media demo là mô phỏng; xác minh nguồn/giấy phép trước khi công khai.
- Lỗi còn tồn tại và giới hạn: [điền trung thực sau kiểm thử]

## 10. Khai báo sử dụng công cụ AI
- Tệp khai báo chi tiết: [đường dẫn tệp nhóm tạo theo yêu cầu học phần]
- Mô tả phạm vi AI hỗ trợ: [điền theo quy định của giảng viên; không khai báo sai]
