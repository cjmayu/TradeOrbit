# **TradeOrbit System**
> **NTUIM 112-1 Database Management Fianl Project | Group 4**

## File Structure

- **`README`**: Provides setup instructions and information about the project.
- **`config.php`**：Get database information for TradeOrbit System.
- **`register.php`**: The register page of the TradeOrbit System that user can register the TradeOrbit System account.
- **`login.php`**: The login page of the TradeOrbit System that user can log in the TradeOrbit System by the account.
- **`db_password.txt`**: The password for database.
- **`product_page.php`**: Home page of TradOrbit System from user perspective, containing all products.
- **`user_page.php`**: Individual page of user from user perspective, containing basic information of users.
- **`user_following.php`**: Users can see the list of theirfollowing stores in this page.
- **`cart.php`**: User cart interface, containing all products that users add to their cart.
- **`addToCart.php`**: The code for users adding products to cart.
- **`customer_view_one_market.php`**: The interface that users look other store pages.
- **`edit_product.php`**: The interface that users can edit products in their stores.
- **`myorder.php`**: The interface for users past orders.
- **`order_detail.php`**: The interface that users look detail of their ecah past order.
- **`seller_market.php`**: The interface for each user's own store page.
- **`prepare.php`**: The interface that users can look what orders and products they need to prepare (orders of their stores).
- **`order_processing.php`**: The interface for users to ensure the detail of an order after place an order from their cart and before submit the order to 
the system.
- **`order_include.php`**: The code to insert the new orders and remove the quantities of stock of the products users just baught.
- **`admin_page.php`**: Administrator porfile interface.
- **`admin_product_page.php`**: Home page of TradOrbit System from administrator perspective, containing all products.
- **`admin_see_user.php`**: The interface for administrator to get or delete the users' information.
- **`admin_view_store.php`**: The interface for administrator to get or delete individual user's store.
- **`style1.css`**: Contains the CSS styles for the project's frontend.

## **Installation and Setup**

### **Step 1: System Requirements** 

- PHP 7.4+
- XAMPP
- Composer
- PostgreSQL

Please go through the following steps to set up your environment

### **Step 2: XAMPP Installation**

Download and install XAMPP from the [official website](https://www.apachefriends.org/index.html). If you do not plan to use MySQL, you may unselect it. 

### **Step 3: Composer Installation**

Install Composer from the [official website](https://getcomposer.org/download/).

### **Step 4: Project Deployment**

Unzip the provided project archive into the **`htdocs`** directory of XAMPP.

### **Step 5: Composer Dependencies**

Open your system console, navigate to the project folder, and execute **`composer install`** to install the required PHP packages, including Eloquent ORM. 

### **Step 6: Database Configuration** (skip this step if you have done so before)

Please go to https://drive.google.com/file/d/172TFT-MzRx7qM1immb1UVEFOu_olat6m/view?usp=sharing to download the sql file.
Create a PostgreSQL database named **`TradeOrbit`**. Import the provided **`.sql`** file to populate your database with the necessary tables and data.

### **Step 7: Eloquent Configuration**

Configure the database connection in **`config.php`** with your PostgreSQL credentials. Put your password in **`db_password.txt`**. 

### **Step 8: PHP Connection Settings**

Modify the database connection settings in **`config.php`** file to match your PostgreSQL credentials (You need to change host, port, dbname and user to your own). Put your password in **`db_password.txt`**. 

### **Step 9: Installing PostgreSQL driver for PHP**

Go to your PHP directory (e.g., at **`C:\xampp\php`**) to edit php.ini using any plain text editor. Uncomment **`;extension=pdo_pgsql`** and **`;extension=pgsql'** by removing the semicolons. 









## **Running the Application**

### **Starting Apache**

After installation, start the Apache web server. To start Apache, go to the right directory (e.g., C:\xampp\apache\bin) to execute httpd.exe. If everything goes well, you may see the Apache homepage at **`http://localhost/`**.  

### **See the index page**

Access the system via **`http://localhost/your-project-folder/`** in your web browser.

### **User Interface**

Navigate to **`register.php`** for register an account and **`login.php`** for log in the system.

### **Admin Interface**

Navigate to **`login.php`**, use email: "hector60@gmail.com" and password "(%Nb!6Awl9" to log in the system as an admin.

### **Database Performance**

To improve performance, create indexes on the **`product`** tables for the **`productid`** column, the **`product`** tables for the **`product_name`** column** and **`comment`** table for the **`productid`** and **`star`** columns.



