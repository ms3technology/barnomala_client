# Barnomala API Documentation

Base URL: `/api/v1`

## Authentication

All sync endpoints (POST requests) require authentication via the `api.token` middleware.

**Header:**
```
Authorization: Bearer {your-api-token}
```

---

## File Upload

### Upload Files
```http
POST /upload
```

Upload multiple files to the server.

**Request:**
- Content-Type: `multipart/form-data`

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| files | array | Yes | Array of files (max 10MB each) |
| folder | string | No | Target folder (default: 'uploads') |

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "name": "document.pdf",
      "path": "uploads/document.pdf",
      "url": "/storage/uploads/document.pdf",
      "size": 1024000,
      "mime_type": "application/pdf"
    }
  ],
  "errors": [],
  "summary": {
    "uploaded": 1,
    "failed": 0
  }
}
```

---

## Sync Endpoints (Protected)

### Teachers Sync
```http
POST /teachers/sync
```

**Request Body:**
```json
{
  "teachers": [
    {
      "id": 123,
      "teacher_name": "John Doe",
      "designation": "Assistant Teacher",
      "department": "Science",
      "father_name": "Father Name",
      "mother_name": "Mother Name",
      "blood_group": "A+",
      "religion": "Islam",
      "present_address": "Dhaka",
      "permanent_address": "Chittagong",
      "gender": "Male",
      "priority_index": 1,
      "photo": "/path/to/photo.jpg",
      "teacher_code": "T001",
      "phone": "01712345678",
      "email": "teacher@example.com",
      "joining_date": "2020-01-15",
      "experience_years": 5,
      "mpo": "Yes",
      "status": true
    },
    {
      "id": 456
    }
  ]
}
```

**Note:** Send only `{"id": 456}` to delete the teacher.

**Response:**
```json
{
  "status": "success",
  "summary": {
    "updated": 1,
    "deleted": 1,
    "failed": 0
  }
}
```

---

### Staff Sync
```http
POST /staff/sync
```

**Request Body:**
```json
{
  "staff": [
    {
      "id": 789,
      "name": "Jane Smith",
      "staff_code": "S001",
      "department": "Administration",
      "designation": "Clerk",
      "gender": "Female",
      "date_of_birth": "1990-05-20",
      "phone": "01787654321",
      "email": "staff@example.com",
      "photo": "/path/to/photo.jpg",
      "national_id": "1234567890",
      "religion": "Islam",
      "blood_group": "B+",
      "marital_status": "Married",
      "present_address": "Dhaka",
      "permanent_address": "Sylhet",
      "joining_date": "2019-03-10",
      "leaving_date": null,
      "status": "active"
    }
  ]
}
```

---

### Committees Sync
```http
POST /committees/sync
```

**Request Body:**
```json
{
  "committees": [
    {
      "id": 1,
      "committee_type": "general",
      "name": "Academic Committee",
      "session": "2024-2025",
      "description": "Manages academic affairs",
      "order_index": 1,
      "status": "active",
      "note": null,
      "members": [
        {
          "id": 1,
          "name": "Member Name",
          "designation": "Chairman",
          "role": "President",
          "photo": "/path/to/photo.jpg",
          "phone": "01712345678",
          "email": "member@example.com",
          "order_index": 1
        }
      ]
    }
  ]
}
```

---

### Notices Sync
```http
POST /notices/sync
```

**Request Body:**
```json
{
  "notices": [
    {
      "id": 1,
      "title": "Important Notice",
      "content": "Notice details here...",
      "published_at": "2024-01-15",
      "is_active": true,
      "is_urgent": false,
      "artifacts": [
        {
          "id": 1,
          "file_path": "/storage/notices/file.pdf",
          "file_name": "document.pdf",
          "file_type": "application/pdf",
          "file_size": 1024000
        },
        {
          "id": 2
        }
      ]
    }
  ]
}
```

**Artifact Operations:**
- Include full object with `id` → Update existing artifact
- Include without `id` → Create new artifact
- Include only `{"id": X}` → Delete that artifact

**Note:** When deleting a notice (only ID provided), all associated artifacts are automatically deleted.

---

### News Sync
```http
POST /news/sync
```

**Request Body:**
```json
{
  "news": [
    {
      "id": 1,
      "title": "News Headline",
      "summary": "Short summary...",
      "content": "Full article content...",
      "published_at": "2024-01-15",
      "image_json": {"url": "/images/news.jpg", "alt": "News Image"},
      "is_active": true,
      "is_featured": false,
      "artifacts": [
        {
          "id": 1,
          "file_path": "/storage/news/file.pdf",
          "file_name": "document.pdf",
          "file_type": "application/pdf",
          "file_size": 1024000
        }
      ]
    }
  ]
}
```

---

### Galleries Sync
```http
POST /galleries/sync
```

**Request Body:**
```json
{
  "galleries": [
    {
      "id": 1,
      "type": "photo",
      "title": "Annual Sports Day",
      "category": "Sports",
      "date": "2024-01-15",
      "image_path": "/storage/galleries/photo.jpg",
      "video_url": null,
      "video_path": null,
      "description": "Description of the gallery item"
    }
  ]
}
```

**Types:** `photo` | `video`

---

### Speeches Sync
```http
POST /speeches/sync
```

**Request Body:**
```json
{
  "speeches": [
    {
      "id": 1,
      "name": "Principal Name",
      "title": "Principal's Message",
      "designation": "Principal",
      "speech": "Welcome message content...",
      "image_json": {"url": "/images/principal.jpg", "alt": "Principal"},
      "row_index": 1,
      "column_index": 1,
      "colspan": 2,
      "is_active": true
    }
  ]
}
```

---

### Options Sync
```http
POST /options/sync
```

**Request Body:**
```json
{
  "options": {
    "institute.name": "School Name",
    "institute.address": "School Address",
    "institute.phone": "01712345678",
    "theme.primary_color": "#1e40af",
    "institute.demographics.students": 500,
    "institute.stats.teachers": 25
  }
}
```

**Note:** Supports strings, numbers, booleans, and nested JSON objects.

**Response:**
```json
{
  "synced": true,
  "counts": {
    "options": 5
  }
}
```

---

## Data Transfer

### Transfer All Data
```http
POST /transfer/all
```

Triggers a full data transfer operation.

**Response:**
```json
{
  "status": "success",
  "message": "Transfer completed"
}
```

---

### Setup Default Website
```http
POST /setup/default-website
```

Initializes default website configuration.

**Response:**
```json
{
  "status": "success",
  "message": "Default website setup completed"
}
```

---

## Public GET Endpoints

### List Notices
```http
GET /notices?per_page=15
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Important Notice",
      "content": "Notice content...",
      "published_at": "2024-01-15",
      "is_active": true,
      "is_urgent": false,
      "artifacts": [
        {
          "id": 1,
          "notice_id": 1,
          "file_path": "/storage/notices/file.pdf",
          "file_name": "document.pdf",
          "file_type": "application/pdf",
          "file_size": 1024000
        }
      ]
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 50,
    "last_page": 4
  }
}
```

---

### List News
```http
GET /news?per_page=15
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "News Headline",
      "summary": "Summary...",
      "content": "Full content...",
      "published_at": "2024-01-15",
      "image_json": {"url": "/images/news.jpg"},
      "is_active": true,
      "is_featured": false,
      "artifacts": [...]
    }
  ],
  "pagination": {...}
}
```

---

### List Galleries
```http
GET /galleries?per_page=15
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "type": "photo",
      "title": "Event Title",
      "category": "Events",
      "date": "2024-01-15",
      "image_path": "/storage/galleries/photo.jpg",
      "video_url": null,
      "video_path": null,
      "description": "Description"
    }
  ],
  "pagination": {...}
}
```

---

### List Speeches
```http
GET /speeches?per_page=15
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Principal Name",
      "title": "Principal's Message",
      "designation": "Principal",
      "speech": "Message content...",
      "image_json": {"url": "/images/principal.jpg"},
      "row_index": 1,
      "column_index": 1,
      "colspan": 2,
      "is_active": true
    }
  ],
  "pagination": {...}
}
```

---

### List Students
```http
GET /students
```

Returns exported student data.

---

### Student Enrollments
```http
GET /student/enrollments
```

Returns student enrollment data.

---

### List Subjects
```http
GET /subjects
```

Returns subject data.

---

### List Teachers
```http
GET /teachers
```

Returns exported teacher data.

---

### List Exams
```http
GET /exams
```

Returns exam data.

---

### Exam Schedules
```http
GET /exams/schedules
```

Returns exam schedule data.

---

### Exam Results
```http
GET /exams/results
```

Returns exam result data.

---

### Slider Images
```http
GET /slider-images
```

Returns homepage slider image data.

---

### Committees
```http
GET /committees
```

Returns committee data.

---

### Governing Body
```http
GET /governing-body
```

Returns governing body member data.

---

### Options
```http
GET /options
```

Returns all configuration options.

---

## Error Responses

### Validation Error (422)
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "files": ["The files field is required."]
  }
}
```

### Authentication Error (401)
```json
{
  "status": "error",
  "message": "Unauthorized"
}
```

### Server Error (500)
```json
{
  "status": "error",
  "message": "Sync failed",
  "error": "Exception message details"
}
```

---

## Common Patterns

### Sync Delete Pattern
To delete any entity, send only the ID in the array:
```json
{
  "entity": [{"id": 123}]
}
```

### Pagination Query Parameters
All list endpoints support:
- `per_page` - Number of items per page (default: 15)
- `page` - Page number

### Date Formats
All dates should be in ISO 8601 format: `YYYY-MM-DD` or `YYYY-MM-DD HH:MM:SS`

### Image/JSON Fields
Image fields accept JSON objects:
```json
{
  "url": "/path/to/image.jpg",
  "alt": "Image description"
}
```
