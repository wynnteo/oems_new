<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses,course_code',
            'slug' => 'required|string|max:255|unique:courses,slug',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced',
            'instructor' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'is_active' => 'nullable|in:active,inactive,draft',
            'is_featured' => 'nullable|boolean'
        ]);

        $data = $request->except(['thumbnail', '_token']);
        
        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
            $data['thumbnail'] = $thumbnailPath;
        }

        // Handle checkbox value for is_featured
        $data['is_featured'] = $request->has('is_featured') ? true : false;

        // Set defaults if not provided
        $data['difficulty_level'] = $data['difficulty_level'] ?? 'beginner';
        $data['is_active'] = $data['is_active'] ?? 'active';
        $data['language'] = $data['language'] ?? 'English';

        Course::create($data);

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses,course_code,' . $course->id,
            'slug' => 'required|string|max:255|unique:courses,slug,' . $course->id,
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced',
            'instructor' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'is_active' => 'nullable|in:active,inactive,draft',
            'is_featured' => 'nullable|boolean'
        ]);

        $data = $request->except(['thumbnail', '_token', '_method']);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if it exists
            if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
            $data['thumbnail'] = $thumbnailPath;
        }

        // Handle checkbox value for is_featured
        $data['is_featured'] = $request->has('is_featured') ? true : false;

        $course->update($data);

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully');
    }

    public function destroy(Course $course)
    {
        // Delete thumbnail file if it exists
        if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully');
    }

    public function show(Course $course)
    {
        $enrolments = $course->enrolments()->with('student')->get();
        $exams = $course->exams;

        return view('admin.courses.show', compact('course', 'enrolments', 'exams'));
    }

    public function search(Request $request)
    {
        $search = $request->input('q');
        
        if (is_string($search)) {
            $courses = Course::where('title', 'like', "%{$search}%")
                            ->orWhere('course_code', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%")
                            ->orWhere('instructor', 'like', "%{$search}%")
                            ->where('is_active', 'active') // Only search active courses
                            ->get(['id', 'title', 'course_code', 'category']);
        } else {
            $courses = collect(); // Return an empty collection if search input is invalid
        }

        return response()->json($courses);
    }

    public function generateSlug(Request $request)
    {
        $title = $request->input('title');
        if (!$title) {
            return response()->json(['slug' => '']);
        }

        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug exists and make it unique
        while (Course::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return response()->json(['slug' => $slug]);
    }

    public function toggleStatus(Request $request, Course $course)
    {
        $course->update(['is_active' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function toggleFeatured(Request $request, Course $course)
    {
        $course->update(['is_featured' => $request->featured]);
        return response()->json(['success' => true]);
    }
}
