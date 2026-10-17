# Yêu cầu DT-16 — Bảo tàng ảo và tour tham quan 360° trên nền web

Tài liệu này chép lại phạm vi chức năng và phi chức năng trong danh mục yêu cầu của nhóm, để đối chiếu với mã nguồn. Trạng thái tính năng trong ma trận truy vết chỉ thể hiện vị trí dự kiến trong source; cần kiểm thử runtime trước khi kết luận đạt.

## 1. Yêu cầu chức năng

| Mã | Tên yêu cầu | Mô tả |
|---|---|---|
| FR-01 | Đăng ký tài khoản | Cho phép khách tham quan tạo tài khoản để sử dụng các chức năng cá nhân như lưu tour và hiện vật yêu thích. |
| FR-02 | Đăng nhập/Đăng xuất | Cho phép người dùng đăng nhập bằng tài khoản đã đăng ký và đăng xuất khi không còn sử dụng. |
| FR-03 | Quản lý thông tin cá nhân | Cho phép người dùng xem, chỉnh sửa và cập nhật thông tin cá nhân được lưu trên hệ thống. |
| FR-04 | Xem danh sách hiện vật | Hiển thị danh sách hiện vật, tác phẩm và tư liệu đang trưng bày trong bảo tàng ảo. |
| FR-05 | Tìm kiếm hiện vật | Tìm kiếm hiện vật theo tên, từ khóa hoặc thông tin liên quan. |
| FR-06 | Xem chi tiết hiện vật | Hiển thị hình ảnh, tên, nguồn gốc, thời kỳ, chất liệu và nội dung giới thiệu chi tiết khi dữ liệu có sẵn. |
| FR-07 | Tham quan không gian 360° | Cho phép trải nghiệm không gian bảo tàng bằng hình ảnh/môi trường tham quan 360 độ. |
| FR-08 | Điều hướng trong không gian 360° | Cho phép xoay góc nhìn và di chuyển giữa các phòng, khu vực hoặc vị trí tham quan. |
| FR-09 | Xem điểm tương tác | Cho phép lựa chọn các điểm tương tác được đặt trong không gian 360° để khám phá nội dung. |
| FR-10 | Xem thông tin tại điểm tham quan | Hiển thị thông tin về hiện vật, khu vực hoặc địa điểm khi người dùng chọn điểm tương tác. |
| FR-11 | Xem danh sách tour | Hiển thị tour tham quan ảo cùng tên, chủ đề, thời lượng/thông tin mô tả khi dữ liệu có sẵn. |
| FR-12 | Bắt đầu tour tham quan | Cho phép lựa chọn tour và bắt đầu trải nghiệm theo trình tự điểm tham quan được thiết lập. |
| FR-13 | Theo dõi tiến trình tour | Hiển thị vị trí hiện tại, điểm đã tham quan và tiến độ hoàn thành tour. |
| FR-14 | Xem nội dung đa phương tiện | Cho phép xem hình ảnh, video và nghe âm thanh thuyết minh liên quan đến hiện vật hoặc không gian. |
| FR-15 | Yêu thích hiện vật/tour | Cho phép lưu hiện vật hoặc tour yêu thích để truy cập và xem lại. |
| FR-16 | Đánh giá và phản hồi | Cho phép gửi đánh giá, nhận xét và phản hồi về nội dung hoặc trải nghiệm tham quan. |
| FR-17 | Quản lý hiện vật | Cho phép người được phân quyền thêm, sửa, xóa và cập nhật thông tin, hình ảnh hiện vật. |
| FR-18 | Quản lý không gian 360° | Cho phép quản lý không gian, ảnh 360°/điểm nhìn và cấu hình liên quan. |
| FR-19 | Quản lý tour | Cho phép tạo, chỉnh sửa, sắp xếp điểm tham quan và xóa tour. |
| FR-20 | Quản lý tài khoản | Cho phép quản trị viên xem/cập nhật vai trò, khóa hoặc mở khóa tài khoản. |
| FR-21 | Quản lý đánh giá | Cho phép quản trị viên xem, kiểm duyệt và xử lý đánh giá/phản hồi không phù hợp. |
| FR-22 | Thống kê hệ thống | Cung cấp số liệu về lượt truy cập, lượt xem hiện vật, lượt tham quan tour và hoạt động người dùng. |

## 2. Yêu cầu phi chức năng

| Mã | Yêu cầu | Cách xác minh gợi ý | Trạng thái hồ sơ |
|---|---|---|---|
| NFR-01 | Hiệu năng | Đo 5 trang trọng yếu và lập bảng trước/sau tối ưu. | Chưa chạy |
| NFR-02 | Tốc độ tải | Đo LCP, CLS, TBT và dung lượng tài nguyên. | Chưa chạy |
| NFR-03 | Hiển thị 360° | Kiểm tra xoay, phóng/thu và chuyển scene. | Chưa chạy |
| NFR-04 | Khả năng tương thích | Kiểm tra Chrome/Chromium, Edge, Firefox và Safari/WebKit phù hợp môi trường. | Chưa chạy |
| NFR-05 | Responsive | Kiểm tra điện thoại, máy tính bảng và máy tính. | Chưa chạy |
| NFR-06 | Dễ sử dụng | Thử nghiệm với người dùng ngoài nhóm và lưu phát hiện. | Chưa chạy |
| NFR-07 | Bảo mật | Rà soát cấu hình, đầu vào, phiên/token, bí mật và lỗi công khai. | Chưa chạy |
| NFR-08 | Phân quyền | Thử vai trò visitor/editor/admin, bao gồm gọi trực tiếp API. | Chưa chạy |
| NFR-09 | Toàn vẹn dữ liệu | Kiểm tra khóa, ràng buộc, giao dịch và dữ liệu đầu vào. | Chưa chạy |
| NFR-10 | Tính ổn định | Smoke test và thử phương án dự phòng khi dịch vụ phụ ngừng. | Chưa chạy |
| NFR-11 | Khả năng mở rộng | Kiểm tra phân trang và khả năng bổ sung nội dung. | Chưa chạy |
| NFR-12 | Khả năng bảo trì | Kiểm tra cấu trúc, lint, hướng dẫn và trách nhiệm mỗi mô-đun. | Một phần: đã lint cú pháp PHP trong môi trường đóng gói |
| NFR-13 | Sao lưu và phục hồi | Chạy sao lưu, khôi phục vào CSDL kiểm tra riêng và đối chiếu dữ liệu. | Chưa chạy |
| NFR-14 | Khả năng chịu tải | Chạy kiểm thử tải có kiểm soát trên môi trường của nhóm. | Chưa chạy |
| NFR-15 | Tối ưu tài nguyên | Kiểm tra kích thước ảnh 360°, video/audio, lazy-loading và băng thông. | Chưa chạy |
| NFR-16 | Tính chính xác nội dung | Đối chiếu hiện vật và thông tin lịch sử với nguồn có điều kiện sử dụng rõ ràng. | Cần đối chiếu: nội dung gói hiện là mô phỏng |
| NFR-17 | Tính nhất quán giao diện | Rà soát header, theme, responsive, nhãn và điều hướng. | Chưa chạy |

## 3. Liên kết tới ma trận truy vết

- `docs/FEATURE_TRACEABILITY.csv`: đường dẫn source/API tương ứng với 22 yêu cầu chức năng.
- `docs/NON_FUNCTIONAL_CHECKLIST.md`: cách kiểm tra và trạng thái 17 yêu cầu phi chức năng.
- `05_KiemThu/60-ca-kiem-thu.csv`: 60 ca kiểm thử thủ công với trường kết quả thực tế và minh chứng để trống cho tới khi thực thi thật.
