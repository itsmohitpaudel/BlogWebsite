# Laravel RESTful API - Blog System

This is a RESTful API built with Laravel, enabling users to manage posts, categories, tags, and comments with role-based access control.

Features
- User authentication via Laravel Sanctum
- Role-Based Access Control (RBAC) for Admins & Authors
- Polymorphic relationships for comments and tags
- Search & Filtering using Laravel Query Builder
- Pagination for optimized performance
- Input validation & structured error handling

---

## Set Up and Run the Application Locally

### Prerequisites:
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
Database Seeding

For initial testing, the following admin users are pre-seeded into the database. You can use these credentials to log in as an admin:
```
[
    [
        'name' => 'Ram',
        'email' => 'ram@gmail.com',
        'password' => Hash::make('ram@123'),
        'role' => 'admin',
    ],
    [
        'name' => 'Hari',
        'email' => 'hari@gmail.com',
        'password' => Hash::make('hari@123'),
        'role' => 'admin',
    ],
]

```

### Authentication ###

- This API uses Laravel Sanctum for authentication.
- Include the token in the Authorization header:

``
Authorization: Bearer your_token_here
``

### Additional Information
- Role-based access control is implemented with **Admin** and **Author** roles.
- Polymorphic relationships are used for **comments** and **tags**.
- All users registering within the application are assigned the author role by default.

---

## Role-Based Authorization Using Gates
In this project, 
- Role-based access control (RBAC) is implemented using Gates to manage user permissions for different operations.
- The Admin and Author roles are the primary users with different levels of access to the resources in the blog API.

1. **Gates for Authorization**
- I have used Laravel's Gate functionality to define custom authorization logic that checks a user's role before allowing them to perform certain actions.
- This ensures that only users with the appropriate role (Admin or Author) can access certain endpoints.

2. **Roles**
- Admin: Admin users have higher privileges and can manage categories, tags, posts, and users. They can perform actions like creating, updating, or deleting categories,     posts, and tags, and modifying user roles.
- Author: Users with the Author role are allowed to create and update their own posts, as well as post comments. They do not have access to admin functionalities like managing other users or categories.

3. **Authorization Logic:**
- Policies ensure that the users have the appropriate permission to access specific actions, based on their defined role.

4. **Example of Gate Usage:**
    ```
   Gate::authorize('update-post', $post);
    ```

- This checks whether the currently authenticated user is allowed to update the post, based on their role (either Author or Admin).


## API Documentation for Blog API

### Base URL
The base URL for the API is:

    http://127.0.0.1:8000/api

#### Authentication
To access the endpoints, users need to be authenticated using **Laravel Sanctum**.

1. **Login**:
    - URL:
      ```
      http://127.0.0.1:8000/api/login
      ```
    - Use this endpoint to authenticate and retrieve a token.

2. **Logout**:
    - URL:
      ```
      http://127.0.0.1:8000/api/logout
      ```
    - Use this endpoint to invalidate the token.

---

### Routes and Endpoints
All routes below require the user to be authenticated with a valid token.

#### User Routes

**Logged in User:**
- **GET**
  ```
  /api/user
  ```  
  **Description**: Retrieve details of the currently authenticated user.
  - Response:
    - `200 OK`: Returns user data (name, email, etc.)
    - `401 Unauthorized`: If the user is not authenticated.

**Logged in User Posts:**
- **GET**
  ```
  /api/my-posts
  ```  
  **Description**: Get posts created by the currently authenticated user, including category, tags, and comments.
  - Response:
    - `200 OK`: List of posts created by the authenticated user.
    - `401 Unauthorized`: If the user is not authenticated.

---

#### Categories Routes

**Get Categories:**
- **GET**
  ```
  /api/categories
  ```  
  **Description**: Get a list of all categories along with posts.
  - Response:
    - `200 OK`: List of categories with posts.

**Create Category (Admin Only):**
- **POST**
  ```
  /api/categories
  ```  
  **Description**: Create a new category.
  - Response:
    - `200 OK`: Returns the created category.
    - `403 Forbidden`: If the user is not an admin.

**Update Category (Admin Only):**
- **PATCH**
  ```
  /api/categories/category_slug
  ```  
  **Description**: Update a category.
  - Response:
    - `201 Created`: Returns the updated category.
    - `403 Forbidden`: If the user is not an admin.

**Delete Category (Admin Only):**
- **DELETE**
  ```
  /api/categories/category_slug
  ```  
  **Description**: Delete a category.
  - Response:
    - `200 Deleted`: Returns Successful Deletion Message.
    - `403 Forbidden`: If the user is not an admin.
---

#### Posts Routes

**Get All Posts:**
- **GET**
  ```
  /api/posts
  ```  
  **Description**: Retrieve a list of all posts, including author, category, tags, and comments.
  - Response:
    - `200 OK`: List of posts.

**Create Post (Author/Admin Only):**
- **POST**
  ```
  /api/posts
  ```  
  **Description**: Create a new post.
  - Response:
    - `201 Created`: Returns the created post.
    - `403 Forbidden`: If the user is not an author or admin.
   
**Update Post (Author can only update their post / Admin can update any post):**
- **PATCH**
  ```
  /api/posts/{post_slug}
  ```  
  **Description**: Update a post.
  - Response:
    - `201 Created`: Returns the created post.
    - `403 Forbidden`: If the user is not an author or admin.

**Delete Post (Author can only delete their post / Admin can delete any post):**
- **DELETE**
- ```
  /api/posts/{post_slug}
  ```  
  **Description**: Delete a post.
  - Response:
    - `200 Okay`: Returns success message.
    - `403 Forbidden`: If the user is not an author or admin.

---

#### Comments Routes

**Logged in User Comments:**
- **GET**
  ```
  /api/my-comments
  ```  
  **Description**: Get all comments made by the authenticated user.
  - Response:
    - `200 OK`: List of comments made by the user.

**Get Comments:**
- **GET**
  ```
  /api/posts/{post_id}/comments
  ```  
  **Description**: Get all comments for a specific post.
  - Response:
    - `200 OK`: List of comments for the post.
    - `404 Not Found`: If the post is not found.

**Post Comments:**
- **POST**
  ```
  /api/posts/{post_id}/comments
  ```  
  **Description**: Add a comment to a post.
  - Response:
    - `201 Created`: Returns the created comment.
    - `404 Not Found`: If the post is not found.

**Update Comment:**
- **PATCH**
  ```
  /api/comments/{comment_id}
  ```  
  **Description**: Update a comment (only the comment owner can edit, and Admin can edit any comment).
  - Response:
    - `204 No Content`: Successfully edited the comment.
    - `403 Forbidden`: If the user does not own the comment.
    - `404 Not Found`: If the comment is not found.

**Delete Comment:**
- **DELETE**
  ```
  /api/comments/{comment_id}
  ```  
  **Description**: Delete a comment (only the comment owner or the post owner or Admin can delete any comment).
  - Response:
    - `204 No Content`: Successfully deleted the comment.
    - `403 Forbidden`: If the user does not own the comment.
    - `404 Not Found`: If the comment is not found.

---

#### Tags Routes

**Get Tags (Admin Only): **
- **GET**
  ```
  /api/tags
  ```  
  **Description**: Get all tags.
  - Response:
    - `200 OK`: List of tags with all the linked posts.

**Post Tags (Admin Only):**
- **POST**
  ```
  /api/tags
  ```  
  **Description**: Post tags.
  - Response:
    - `200 OK`: Returns the updated post with attached tags.
    - `404 Not Found`: If the post is not found.

**Edit Tags (Admin Only):**
- **PATCH**
  ```
  /api/tags/tag_slug
  ```  
  **Description**: Edit tags.
  - Response:
    - `200 OK`: Returns success message with edited tag details.
    - `404 Not Found`: If the tag is not found.

**Delete Tags (Admin Only):**
- **DELETE**
  ```
  /api/tags/tag_slug
  ```  
  **Description**: Delete tags.
  - Response:
    - `200 OK`: Returns successfully delete message.
    - `404 Not Found`: If the tag is not found.


---

#### Role Routes

**Update User Role (Admin Only):**
- **PATCH**
  ```
  /api/users/{id}/update-role
  ```  
  **Description**: Update the role of a user (author).
  - Response:
    - `200 OK`: Returns the updated user.
    - `403 Forbidden`: If the user is not an admin.

---

#### Search and Filter Route

**Search and Filter Posts:**
- **GET**
  ```
  /api/search
  ```  
  **Description**: Search posts based on title, author, category, or tags.
  *Query Parameters for search:*
  Filter by title:
  ```
  /api/search?filter[title]=post_title
  ```
  Filter by author name:
  ```
  /api/search?filter[author]=author_name
  ```
  Filter by category name:
  ```
  /api/search?filter[category]=category_name
  ```
  Filter by tag name:
  ```
  /api/search?filter[tag]=tag_name
  ```

---
