# MyShop

MyShop is a Laravel-based e-commerce web application that makes online shopping simple and enjoyable. With separate roles for users and admins, MyShop streamlines product browsing, checkout, discounts, and store management.

## Features

### For Users (Customers)

- **Browse Products:** Users can scroll through all available products, view details, and check prices.
- **Product Details:** Easily review product descriptions, prices, and other relevant information.
- **Login Required for Purchase:** To purchase any items, users must register or log in to their account.
- **Coupons & Discounts:** Users can apply coupon codes to receive discounts during checkout.
- **Checkout:** Secure and straightforward checkout process integrated into the platform.

### For Admins

- **Add Products:** Admins can add new products to the store, specifying categories and subcategories for organization.
- **Manage Coupons:** Easily create and manage discount coupons that users can apply during checkout.
- **Flexible Discounts:** Apply discounts to individual products, entire classes, or subclasses of products, or by code/name.
- **Company Turnover Insights:** View total sales/turnover of the company directly from the dashboard.

## Roles & Permissions

- **User:** Can view products, apply coupons, and checkout after logging in.
- **Admin:** Has full management access to products, coupons, discounts, and reporting.

## Getting Started

To run this project locally:

1. **Clone the repository**
   ```bash
   git clone https://github.com/siambasher123/MyShop.git
   cd MyShop
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Set up environment**
   - Copy `.env.example` to `.env`
   - Configure your database and other environment variables

4. **Migrate and seed the database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start the local server**
   ```bash
   php artisan serve
   ```

6. **Access MyShop:**
   - Open your browser and go to [http://localhost:8000](http://localhost:8000)

## Usage

- **User Registration**: Sign up with an email and password to start shopping.
- **Admin Access**: Log in as admin to manage products, coupons, view sales, and more.

## Screenshots

_Add screenshots here to give a visual overview of the app if available._

## Contributing

Contributions are welcome! Please open an issue or a pull request if you have improvements or fixes.

## License

This project is open-source. [Specify your license here.]