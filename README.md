# Daar Al Quran

A Laravel-based application for managing Quran teaching schools, students, teachers, and classrooms.

## Features

- Student management system
- Teacher dashboard
- Classroom organization
- Messaging system between teachers and students
- Administrative tools for school management
- **🆕 Quran Memorization Tracking** - Track and monitor student progress in Quran memorization

### Quran Memorization Tracking

The system now includes comprehensive Quran memorization tracking capabilities:

#### For Teachers:
- **Surah-by-Surah Tracking**: Monitor each student's progress through all 114 Surahs of the Quran
- **Progress States**: Track four different memorization states:
  - لم يبدأ (Not Started)
  - قيد الحفظ (In Progress)
  - محفوظة (Memorized)
  - مراجعة (Under Review)
- **Teacher Notes**: Add personalized notes for each Surah's memorization status
- **Visual Dashboard**: Beautiful visual interface showing completion statistics
- **Dashboard Statistics**: View memorization progress summary on the teacher dashboard

#### Features:
- Complete list of all Quran Surahs in Arabic
- Click-to-update progress tracking
- Automatic timestamping of progress milestones
- Teacher verification system
- Responsive design for all devices
- Integration with existing student management

#### How to Use:
1. Navigate to any classroom's student list
2. Click the green Quran icon (📖) next to any student
3. View the student's memorization progress grid
4. Click on any Surah card to update its status
5. Add teacher notes and save progress
6. Monitor overall statistics from the teacher dashboard

## Requirements

- PHP 7.4+
- Laravel 8.x
- MySQL 5.7+
- Composer

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database settings
4. Run `php artisan key:generate`
5. Run `php artisan migrate`
6. (Optional) Run `php artisan db:seed --class=MemorizationProgressSeeder` to add sample data
7. Run `php artisan serve`

## New Database Tables

### Memorization Progress
The `memorization_progress` table tracks individual Surah memorization for each student:

- **student_id**: Links to the student
- **surah_number**: Surah number (1-114)
- **surah_name**: Arabic name of the Surah
- **status**: Current memorization status
- **teacher_id**: Teacher who verified/updated the status
- **notes**: Teacher's notes about the student's progress
- **started_at**: When memorization began
- **completed_at**: When memorization was completed
- **last_reviewed_at**: Last review date

## API Endpoints

### Memorization Tracking
- `GET /teacher/students/{student}/memorization` - View student's memorization progress
- `POST /teacher/students/{student}/memorization` - Update memorization status
- `GET /teacher/students/{student}/memorization/{surahNumber}` - Get specific Surah progress

## License

The Daar Al Quran application is private software. 