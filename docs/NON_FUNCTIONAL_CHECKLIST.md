# Checklist yêu cầu phi chức năng DT-16

Đánh dấu trạng thái **chỉ sau khi đo/kiểm thử thật**. Không đánh dấu mặc định là PASS.

| Mã | Yêu cầu | Cách xác minh | Kết quả |
|---|---|---|---|
| NFR-01 | Hiệu năng | Đo 5 trang trọng yếu bằng Lighthouse trước/sau tối ưu | Chưa chạy |
| NFR-02 | Tốc độ tải | Ghi LCP, CLS, TBT và dung lượng tài nguyên | Chưa chạy |
| NFR-03 | Hiển thị 360° | Xoay, phóng/thu và chuyển scene trên panorama demo | Chưa chạy |
| NFR-04 | Tương thích trình duyệt | Chromium, Firefox, WebKit; kiểm tra thêm Edge/Safari nếu có | Chưa chạy |
| NFR-05 | Responsive | Kiểm tra 390x844, 768x1024, 1440x900 | Chưa chạy |
| NFR-06 | Dễ sử dụng | Thử nghiệm khả dụng với 5 người ngoài nhóm | Chưa chạy |
| NFR-07 | Bảo mật | Rà soát OWASP Top 10, secret scan, cấu hình production | Chưa chạy |
| NFR-08 | Phân quyền | Visitor/editor/admin; gọi trực tiếp API không đủ quyền | Chưa chạy |
| NFR-09 | Toàn vẹn dữ liệu | Ràng buộc quan hệ, unique, transaction, test đầu vào | Chưa chạy |
| NFR-10 | Ổn định | Smoke test và thử service fallback | Chưa chạy |
| NFR-11 | Mở rộng | Kiểm tra pagination và khả năng thêm scene/tour | Chưa chạy |
| NFR-12 | Bảo trì | PHP lint, structure review, README cập nhật | Một phần: PHP lint tĩnh; cần kiểm tra lại local |
| NFR-13 | Sao lưu/phục hồi | Backup local và restore thử vào database test riêng | Chưa chạy |
| NFR-14 | Chịu tải | Chạy thử tải có kiểm soát trên môi trường của nhóm | Chưa chạy |
| NFR-15 | Tối ưu media | Kiểm tra dung lượng panorama/audio/video, lazy-loading | Chưa chạy |
| NFR-16 | Chính xác nội dung | Đối chiếu mọi hiện vật với nguồn bảo tàng có giấy phép | Cần thay thế/đối chiếu: dữ liệu gói là mô phỏng |
| NFR-17 | Nhất quán giao diện | Rà soát header, theme, responsive và thuật ngữ | Chưa chạy |
