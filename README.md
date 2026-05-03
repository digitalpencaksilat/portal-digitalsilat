# Portal Digital Silat

**Portal Digital Silat** is a web-based platform designed to manage and display Pencak Silat events. Built with CodeIgniter 3 and Bootstrap 5, it provides a seamless experience for users to browse events and for administrators to manage them.

## 🚀 Features

### Public Portal
- **Dynamic Landing Page**: Features a responsive carousel and event highlights.
- **Event Listing**: Paginated view of all Pencak Silat events with AJAX-powered navigation for smooth transitions.
- **Event Status**: Clear indicators for event statuses such as *Open Registration*, *Coming Soon*, and *Finished*.
- **Visitor Tracking**: Built-in system to track unique daily visitors via IP addresses.

### Admin Dashboard
- **Statistics Overview**: Real-time stats for total events, active registrations, and visitor counts (today & total).
- **Event Management (CRUD)**: Easily add, update, and delete events, including poster uploads.
- **Site Settings**: Manage contact information (WhatsApp, Email, Social Media) dynamically from the dashboard.
- **Secure Authentication**: Protected admin area with login/logout functionality.

## 🛠 Tech Stack

- **Backend**: [CodeIgniter 3](https://codeigniter.com/) (PHP)
- **Frontend**: HTML5, CSS3, JavaScript (AJAX), [Bootstrap 5](https://getbootstrap.com/)
- **Database**: MySQL / MariaDB
- **Asset Management**: Custom posters and carousel images.

## 📦 Installation

To run this project locally, follow these steps:

1. **Clone the Repository**
   ```bash
   git clone https://github.com/digitalpencaksilat/portal-digitalsilat.git
   cd portal-digitalsilat
   ```

2. **Configure Database**
   - Create a database named `digitalsilat_website`.
   - Import the database schema (if provided) or create the necessary tables (`users`, `events`, `visitors`, `site_settings`).
   - Update `application/config/database.php` with your local database credentials:
     ```php
     'hostname' => 'localhost',
     'username' => 'your_username',
     'password' => 'your_password',
     'database' => 'digitalsilat_website',
     ```

3. **Configure Base URL**
   - Open `application/config/config.php` and set your `base_url`:
     ```php
     $config['base_url'] = 'http://localhost/portal-digitalsilat/';
     ```

4. **Run the Application**
   - Place the project in your web server root (e.g., `htdocs` for XAMPP).
   - Access the portal via `http://localhost/portal-digitalsilat/`.
   - Access the admin dashboard via `http://localhost/portal-digitalsilat/index.php/admin`.

## 📷 Screenshots

*(Coming Soon: Add your project screenshots here to showcase the UI)*

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request or open an Issue if you find any bugs or have feature suggestions.

## 📄 License

This project is licensed under the MIT License - see the [license.txt](license.txt) file for details.

---
Developed with ❤️ for the Pencak Silat Community.
