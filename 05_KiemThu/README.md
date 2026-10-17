# Hồ sơ kiểm thử DT-16

- `60-ca-kiem-thu.csv`: đúng 60 testcase theo 13 trường, ba nhóm kịch bản trở lên; `KetQuaThucTe`, người thực hiện, ngày và minh chứng còn trống cho đến khi thực thi.
- `../docs/TEST_MATRIX_60.md`: ma trận tổng quan từ source baseline.
- `../tests/e2e/`: 40 định nghĩa Playwright.
- `../backend/tests/`: 20 định nghĩa PHPUnit.

Không điền PASS tự động. Trạng thái ban đầu là `Chưa chạy`. Khi chạy test, lưu output/screenshot thực tế rồi cập nhật từng testcase và đối chiếu với dữ liệu thật của nhóm.
