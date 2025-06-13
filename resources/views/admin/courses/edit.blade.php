@extends('layouts.master')

@section('title')
Courses | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize">Edit Course</h5>
                    <div class="actions_item">
                        <a class="btn btn-darken" href="{{ route('courses.index') }}" title="Back">
                            <i class="material-icons">arrow_back</i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body pb-2">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible text-white">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Course Code -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Course Code:</strong>
                                    <input type="text" name="course_code" class="form-control" placeholder="Course Code"
                                        value="{{ old('course_code', $course->course_code) }}" required>
                                </div>
                            </div>
                            
                            <!-- Course Title -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Course Name:</strong>
                                    <input type="text" name="title" class="form-control" placeholder="Course Title"
                                        value="{{ old('title', $course->title) }}" required>
                                </div>
                            </div>
                            
                            <!-- Slug -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Slug:</strong>
                                    <input type="text" name="slug" class="form-control" placeholder="course-slug"
                                        value="{{ old('slug', $course->slug) }}" required>
                                    <small class="form-text text-muted">URL-friendly version of the course title</small>
                                </div>
                            </div>
                            
                            <!-- Category -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Category:</strong>
                                    <input type="text" name="category" class="form-control" placeholder="Course Category"
                                        value="{{ old('category', $course->category) }}">
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Description:</strong>
                                    <textarea class="form-control" name="description" rows="4"
                                        placeholder="Course description">{{ old('description', $course->description) }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <strong>Price:</strong>
                                    <input type="number" step='0.01' name="price" class="form-control"
                                        placeholder="0.00" value="{{ old('price', $course->price) }}">
                                </div>
                            </div>
                            
                            <!-- Difficulty Level -->
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <strong>Difficulty Level:</strong>
                                    <select name="difficulty_level" class="form-control">
                                        <option value="beginner" {{ old('difficulty_level', $course->difficulty_level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="intermediate" {{ old('difficulty_level', $course->difficulty_level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ old('difficulty_level', $course->difficulty_level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Duration -->
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <strong>Duration (hours):</strong>
                                    <input type="number" name="duration" class="form-control" placeholder="Duration in hours"
                                        value="{{ old('duration', $course->duration) }}">
                                </div>
                            </div>
                            
                            <!-- Instructor -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Instructor:</strong>
                                    <input type="text" name="instructor" class="form-control" placeholder="Instructor Name"
                                        value="{{ old('instructor', $course->instructor) }}">
                                </div>
                            </div>
                            
                            <!-- Language -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Language:</strong>
                                    <input type="text" name="language" class="form-control" placeholder="Language"
                                        value="{{ old('language', $course->language) }}">
                                </div>
                            </div>
                            
                            <!-- Video URL -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Video URL:</strong>
                                    <input type="url" name="video_url" class="form-control" placeholder="https://example.com/video"
                                        value="{{ old('video_url', $course->video_url) }}">
                                </div>
                            </div>
                            
                            <!-- Thumbnail -->
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Thumbnail:</strong>
                                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                                    @if($course->thumbnail)
                                        <div class="mt-2">
                                            <small class="text-muted">Current thumbnail:</small><br>
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Current thumbnail" 
                                                 class="img-thumbnail" style="max-width: 150px; max-height: 100px;">
                                        </div>
                                    @endif
                                    <small class="form-text text-muted">Upload new thumbnail image (leave empty to keep current)</small>
                                </div>
                            </div>
                            
                            <!-- Tags -->
                            <div class="col-xs-12 col-sm-12 col-md-8">
                                <div class="form-group">
                                    <strong>Tags:</strong>
                                    <input type="text" name="tags" class="form-control" placeholder="tag1, tag2, tag3"
                                        value="{{ old('tags', $course->tags) }}">
                                    <small class="form-text text-muted">Separate tags with commas</small>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <select name="is_active" class="form-control">
                                        <option value="active" {{ old('is_active', $course->is_active) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('is_active', $course->is_active) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="draft" {{ old('is_active', $course->is_active) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Featured -->
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" 
                                               id="is_featured" {{ old('is_featured', $course->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            <strong>Featured Course</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center pt-3">
                                <button type="submit" class="btn bg-gradient-dark btn-lg px-5">
                                    <i class="material-icons">update</i> Update Course
                                </button>
                                <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary btn-lg px-5 ms-3">
                                    <i class="material-icons">cancel</i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


    @section('scripts')
    <script>

    </script>
    @endsection