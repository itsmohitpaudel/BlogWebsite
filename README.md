## Blog API project

## Set Up and Run the Application Locally

### Prerequisites
Before you start, make sure you have the following installed:
- PHP (Recommended version: 8.x or higher)
- Composer
- MySQL
- Node.js

### Steps:
1. Clone the repository:
   git clone https://github.com/itsmohitpaudel/BlogWebsite.git

2. Go inside the folder:
   cd BlogWebsite

3. Install dependencies:
   composer install

4. Set up the environment:
   Copy .env.example to .env
   Configure database in .env

5. Run migrations and seeders:
   php artisan migrate
   php artisan db:seed
   php artisan migrate:fresh --seed

6. Start the development server:
   php artisan serve

## Additional Information
1. Role-based access control is implemented: Admin and Author roles.
2. Polymorphic relationships are used for comments and tags.


## API Documentation for Blog API

## Base URL

The base URL for the API is:

http://127.0.0.1:8000/api

#### Authentication
To access the endpoints, users need to be authenticated using Laravel Sanctum.

1. Login: Use the /login endpoint to authenticate and retrieve a token.
2. Logout: Use the /logout endpoint to invalidate the token.

### Routes and Endpoints
All routes below require the user to be authenticated with a valid token.

### User Routes
1. GET api/user
   Description: Retrieve details of the currently authenticated user.

### Response:
200 OK: Returns user data (name, email, etc.).
401 Unauthorized: If the user is not authenticated.

### Logged in User Posts
2. GET api/my-posts
   Description: Get posts created by the currently authenticated user along with category, tags, comments.

### Response:
200 OK: List of posts created by the authenticated user.
401 Unauthorized: If the user is not authenticated.

### Categories Routes
3. GET api/categories
   Description: Get a list of all categories along with posts.

### Response:
200 OK: List of categories.

This gets all the categories along with the posts belonging to that category.

### Gets all Categories
4. GET api/categories
   Description: Gets all categories

### Response:
201 Created: Returns the created category.
403 Forbidden: If the user is not an admin.

### Posts Categories
5. POST /api/categories
   Description: This post creates a new category and returns the newly created category.

### Posts Routes
5. GET api/posts
   Description: Retrieve a list of all posts with author, category, tags, and comments.

Response:
200 OK: List of posts.

6. POST /posts
   Description: Create a new post (Author and Admin only).

Response:
201 Created: Returns the created post.
403 Forbidden: If the user is not an author or an admin

7. GET /posts/{id}/comments
   Description: Get all comments for a specific post.

Response:
200 OK: List of comments for the post.
404 Not Found: If the post is not found.

8. POST /posts/{id}/comments
   Description: Add a comment to a post.

Response:
201 Created: Returns the created comment.
404 Not Found: If the post is not found.

9. DELETE /comments/{id}
    Description: Delete a comment (Only the comment owner or the post owner can delete).

Response:
204 No Content: Successfully deleted the comment.
403 Forbidden: If the user does not own the comment.
404 Not Found: If the comment is not found.

### Tags Routes
10. GET /posts/{id}/tags
    Description: Get all tags for a specific post.

Response:
200 OK: Gets all tags for the post.

11. POST /posts/{id}/tags
    Description: Attach tags to a specific post.

Response:
200 OK: Returns the updated post with attached tags.
404 Not Found: If the post is not found.

### Comments Routes
12. GET /my-comments
    Description: Get all comments made by the authenticated user.

Response:
200 OK: List of comments made by the user.

### Admin Only Routes
13. PATCH /users/{id}/update-role
    Description: Update the role of a author (Admin only).

Response:
200 OK: Returns the updated user.
403 Forbidden: If the user is not an admin.

### Search Route
14. GET /search
    Description: Search posts based on title, author, category, or tags.

### Query Parameters for search:
1. title: Filter posts by title.
   api/search?filter[title]=post_title
3. author: Filter posts by author name.
   api/search?filter[author]=author_name
5. category: Filter posts by category name.
   api/search?filter[category]=category_name
7. tags: Filter posts by tag name.
   api/search?filter[tag]=tag_name


