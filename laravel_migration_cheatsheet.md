
# Laravel Migration Cheatsheet

## 📌 1. Quy tắc đặt tên file migration

| Mục đích | Tên file migration |
|---------|--------------------|
| Tạo bảng `bookings` | create_bookings_table |
| Thêm cột `parent_id` vào `bookings` | add_parent_id_to_bookings_table |
| Xoá cột `extension_id` khỏi `payment_details` | remove_extension_id_from_payment_details_table |
| Đổi tên cột `check_in` thành `arrival_time` | rename_check_in_to_arrival_time_in_bookings_table |
| Đổi tên bảng `bookings` thành `room_bookings` | rename_bookings_to_room_bookings_table |
| Xoá bảng `booking_extensions` | drop_booking_extensions_table |
| Nhiều thay đổi trong bảng `payment_details` | update_payment_details_table_for_booking_extension_removal |

## 📌 2. Các lệnh thường dùng trong migration

### ✅ Tạo bảng mới

```php
Schema::create('table_name', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});
```

### ✅ Thêm cột vào bảng

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('phone')->nullable();
});
```

### ❌ Xoá cột

```php
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn('phone');
});
```

### 🔁 Đổi tên cột

```php
Schema::table('users', function (Blueprint $table) {
    $table->renameColumn('full_name', 'name');
});
```

### 🔄 Đổi tên bảng

```php
Schema::rename('old_table', 'new_table');
```

### 🧹 Xoá bảng

```php
Schema::dropIfExists('booking_extensions');
```

### 🔑 Khóa ngoại

```php
$table->foreignId('user_id')->constrained()->onDelete('cascade');
```

### 🧪 Ràng buộc check

```php
$table->integer('guest_count')->check('guest_count > 0');
```

### 🔢 Enum

```php
$table->enum('status', ['PENDING', 'CONFIRMED'])->default('PENDING');
```

### ✅ Kiểu dữ liệu phổ biến

| Laravel | SQL | Ví dụ |
|---------|-----|-------|
| `$table->id()` | BIGINT AUTO_INCREMENT | Primary key |
| `$table->string()` | VARCHAR(255) | Tên |
| `$table->text()` | TEXT | Mô tả |
| `$table->integer()` | INT | Tuổi |
| `$table->boolean()` | TINYINT(1) | true/false |
| `$table->decimal(10,2)` | DECIMAL | Giá tiền |
| `$table->float()` | FLOAT | Trọng lượng |
| `$table->dateTime()` | DATETIME | Ngày giờ |
| `$table->timestamps()` | 2 cột: created_at, updated_at |

## 🔁 Hàm down()

Ngược lại với up():
```php
public function down(): void {
    Schema::dropIfExists('bookings');
}
```

## 🎁 Gợi ý khác

- `--create=table_name`: tạo bảng mới
- `--table=table_name`: sửa bảng cũ
