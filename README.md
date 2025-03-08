# Blog API Project

## Set Up and Run the Application Locally

### Prerequisites
Before you start, make sure you have the following installed:
- PHP (Recommended version: 8.x or higher)
- Composer
- MySQL
- Node.js

### Steps:
1. Clone the repository:
    ```bash
    git clone https://github.com/itsmohitpaudel/BlogWebsite.git
    ```

2. Go inside the project folder:
    ```bash
    cd BlogWebsite
    ```

3. Install dependencies:
    ```bash
    composer install
    ```

4. Set up the environment:
    - Copy `.env.example` to `.env`
    - Configure the database in `.env`

5. Run migrations and seeders:
    ```bash
    php artisan migrate:fresh --seed
    ```

6. Start the development server:
    ```bash
    php artisan serve
    ```

### Additional Information
- Role-based access control is implemented with **Admin** and **Author** roles.
- Polymorphic relationships are used for **comments** and **tags**.
- All users registering within the application are assigned the author role by default.

---

## API Documentation for Blog API

### Base URL
The base URL for the API is:

    http://127.0.0.1:8000/api

#### Authentication
To access the endpoints, users need to be authenticated using **Laravel Sanctum**.

1. **Login**:
    - URL:
      `http://127.0.0.1:8000/api/login`
    - Use this endpoint to authenticate and retrieve a token.

2. **Logout**:
    - URL:
      `http://127.0.0.1:8000/api/logout`
    - Use this endpoint to invalidate the token.

---

### Routes and Endpoints
All routes below require the user to be authenticated with a valid token.

#### User Routes

**Logged in User:**
- **GET**
  `/api/user`  
  **Description**: Retrieve details of the currently authenticated user.
  - Response:
    - `200 OK`: Returns user data (name, email, etc.)
    - `401 Unauthorized`: If the user is not authenticated.

**Logged in User Posts:**
- **GET**
  `/api/my-posts`  
  **Description**: Get posts created by the currently authenticated user, including category, tags, and comments.
  - Response:
    - `200 OK`: List of posts created by the authenticated user.
    - `401 Unauthorized`: If the user is not authenticated.

---

#### Categories Routes

**Categories:**
- **GET**
  `/api/categories`  
  **Description**: Get a list of all categories along with posts.
  - Response:
    - `200 OK`: List of categories with posts.

**Create Category (Admin Only):**
- **POST**
  `/api/categories`  
  **Description**: Create a new category.
  - Response:
    - `200 OK`: Returns the created category.
    - `403 Forbidden`: If the user is not an admin.

**Update Category (Admin Only):**
- **PATCH**
  `/api/categories/category_slug`  
  **Description**: Update a category.
  - Response:
    - `201 Created`: Returns the updated category.
    - `403 Forbidden`: If the user is not an admin.

**Delete Category (Admin Only):**
- **DELETE**
  `/api/categories/category_slug`  
  **Description**: Delete a category.
  - Response:
    - `200 Deleted`: Returns Successfull Deletion Message.
    - `403 Forbidden`: If the user is not an admin.
---

#### Posts Routes

**All Posts:**
- **GET** `/api/posts`  
  **Description**: Retrieve a list of all posts, including author, category, tags, and comments.
  - Response:
    - `200 OK`: List of posts.

**Create Post (Author/Admin Only):**
- **POST** `/api/posts`  
  **Description**: Create a new post.
  - Response:
    - `201 Created`: Returns the created post.
    - `403 Forbidden`: If the user is not an author or admin.
   
**Update Post (Author can only update their post / Admin can update any post):**
- **PATCH** `/api/posts/{post_slug}`  
  **Description**: Update a post.
  - Response:
    - `201 Created`: Returns the created post.
    - `403 Forbidden`: If the user is not an author or admin.

**Delete Post (Author can only delete their post / Admin can delete any post):**
- **DELETE** `/api/posts/{post_slug}`  
  **Description**: Delete a post.
  - Response:
    - `200 Okay`: Returns success message.
    - `403 Forbidden`: If the user is not an author or admin.

**Get Comments:**
- **GET** `/api/posts/{post_id}/comments`  
  **Description**: Get all comments for a specific post.
  - Response:
    - `200 OK`: List of comments for the post.
    - `404 Not Found`: If the post is not found.

- **POST** `/api/posts/{post_id}/comments`  
  **Description**: Add a comment to a post.
  - Response:
    - `201 Created`: Returns the created comment.
    - `404 Not Found`: If the post is not found.

**Update Comment:**
- **PATCH** `/api/comments/{comment_id}`  
  **Description**: Update a comment (only the comment owner can edit, and Admin can edit any comment).
  - Response:
    - `204 No Content`: Successfully edited the comment.
    - `403 Forbidden`: If the user does not own the comment.
    - `404 Not Found`: If the comment is not found.

**Delete Comment:**
- **DELETE** `/api/comments/{comment_id}`  
  **Description**: Delete a comment (only the comment owner or the post owner can delete or Admin can delete any comment).
  - Response:
    - `204 No Content`: Successfully deleted the comment.
    - `403 Forbidden`: If the user does not own the comment.
    - `404 Not Found`: If the comment is not found.

---

#### Tags Routes

**Post Tags:**
- **GET** `/api/posts/{id}/tags`  
  **Description**: Get all tags for a specific post.
  - Response:
    - `200 OK`: List of tags for the post.

- **POST** `/api/posts/{id}/tags`  
  **Description**: Attach tags to a post.
  - Response:
    - `200 OK`: Returns the updated post with attached tags.
    - `404 Not Found`: If the post is not found.

---

#### Comments Routes

**User Comments:**
- **GET**
  `/api/my-comments`  
  **Description**: Get all comments made by the authenticated user.
  - Response:
    - `200 OK`: List of comments made by the user.

---

#### Admin Only Routes

**Update User Role (Admin Only):**
- **PATCH**
  `/api/users/{id}/update-role`  
  **Description**: Update the role of a user (author).
  - Response:
    - `200 OK`: Returns the updated user.
    - `403 Forbidden`: If the user is not an admin.

---

#### Search Route

**Search Posts:**
- **GET** `/api/search`  
  **Description**: Search posts based on title, author, category, or tags.
  - Query Parameters for search:
    - Filter by title: `/api/search?filter[title]=post_title`
    - Filter by author name: `/api/search?filter[author]=author_name`
    - Filter by category name: `/api/search?filter[category]=category_name`
    - Filter by tag name: `/api/search?filter[tag]=tag_name`

---
