<?php 
require "header.php";
require "config.php";
require "courses_backend.php";

$courses = new CoursesBackend();

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

$categories = $courses->getCategories();

$courses_list = $courses->getCourses($category_id);
?>

<section class="courses">
   <h1 class="heading">Available Courses</h1>

   <div class="category-filter">
      <h3>Filter by Category:</h3>
      <div class="filter-buttons">
         <a href="courses.php" class="filter-btn <?= !$category_id ? 'active' : '' ?>">All</a>
         <?php foreach($categories as $category): ?>
         <a href="courses.php?category=<?= $category['categore_id'] ?>" 
            class="filter-btn <?= $category_id == $category['categore_id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($category['categore_name']) ?>
         </a>
         <?php endforeach; ?>
      </div>
   </div>

   <div class="search-box">
      <form id="search-form" onsubmit="return false;">
         <input type="text" id="search-input" placeholder="Search courses..." autocomplete="off">
         <button type="submit" id="search-btn"><i class="fas fa-search"></i></button>
      </form>
   </div>

   <div class="box-container" id="courses-container">
      <?php if(empty($courses_list)): ?>
         <p class="empty">No courses found in this category.</p>
      <?php else: ?>
         <?php foreach ($courses_list as $course): ?>
         <div class="box">
            <div class="tutor">
               <img src="<?= htmlspecialchars($course['profile_image'] ?? 'images/default-avatar.jpg') ?>" alt="Tutor Image">
               <div class="info">
                  <h3><?= htmlspecialchars($course['tutor_name'] ?? 'Tutor') ?></h3>
               </div>
            </div>
            <div class="thumb">
               <img src="<?= htmlspecialchars($course['image_url'] ?? 'images/default-course.jpg') ?>" alt="Course Image">
            </div>
            <h3 class="title"><?= htmlspecialchars($course['course_name']) ?></h3>
            <p class="category"><?= htmlspecialchars($course['categore_name'] ?? 'Uncategorized') ?></p>
            <p class="price">$<?= number_format($course['price'], 2) ?></p>
            <a href="playlist.php?course_id=<?= $course['course_id'] ?>" class="inline-btn">View Content</a>
         </div>
         <?php endforeach; ?>
      <?php endif; ?>
   </div>
</section>

<style>
.category-filter {
   margin: 2rem 0;
   text-align: center;
}

.filter-buttons {
   display: flex;
   flex-wrap: wrap;
   gap: 1rem;
   justify-content: center;
   margin-top: 1rem;
}

.filter-btn {
   padding: 0.5rem 1.5rem;
   border-radius: 0.5rem;
   background: var(--light-bg);
   color: var(--black);
   font-size: 1.1rem;
   cursor: pointer;
   transition: all 0.3s ease;
}

.filter-btn:hover {
   background: var(--main-color);
   color: var(--white);
}

.filter-btn.active {
   background: var(--main-color);
   color: var(--white);
}

.search-box {
   display: flex;
   justify-content: center;
   margin: 2rem 0;
   gap: 1rem;
}

.search-box form {
   display: flex;
   width: 50%;
   gap: 1rem;
}

.search-box input {
   flex: 1;
   padding: 1rem;
   border-radius: 0.5rem;
   border: 1px solid var(--light-bg);
   font-size: 1.1rem;
}

.search-box button {
   padding: 1rem 2rem;
   border-radius: 0.5rem;
   background: var(--main-color);
   color: var(--white);
   cursor: pointer;
   transition: all 0.3s ease;
   border: none;
}

.search-box button:hover {
   background: var(--black);
}

.empty {
   text-align: center;
   font-size: 1.5rem;
   color: var(--light-color);
   margin: 2rem 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    const coursesContainer = document.getElementById('courses-container');

    function performSearch(e) {
        if (e) {
            e.preventDefault();
        }
        
        const searchTerm = searchInput.value.trim();
        if (!searchTerm) {
            return;
        }

        // Show loading state
        coursesContainer.innerHTML = '<p class="empty">Searching...</p>';
        
        // Create form data
        const formData = new FormData();
        formData.append('action', 'search');
        formData.append('search_term', searchTerm);

        fetch('courses_backend.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(courses => {
            console.log('Search results:', courses); // Debug log
            
            if (!courses || courses.length === 0) {
                coursesContainer.innerHTML = '<p class="empty">No courses found matching your search.</p>';
                return;
            }
            
            coursesContainer.innerHTML = courses.map(course => `
                <div class="box">
                    <div class="tutor">
                        <img src="${course.profile_image || 'images/default-avatar.jpg'}" alt="Tutor Image">
                        <div class="info">
                            <h3>${course.tutor_name || 'Tutor'}</h3>
                        </div>
                    </div>
                    <div class="thumb">
                        <img src="${course.image_url || 'images/default-course.jpg'}" alt="Course Image">
                    </div>
                    <h3 class="title">${course.course_name}</h3>
                    <p class="category">${course.categore_name || 'Uncategorized'}</p>
                    <p class="price">$${parseFloat(course.price).toFixed(2)}</p>
                    <a href="playlist.php?course_id=${course.course_id}" class="inline-btn">View Content</a>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error:', error);
            coursesContainer.innerHTML = '<p class="empty">Error performing search. Please try again.</p>';
        });
    }

    // Add event listeners
    searchBtn.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });
});
</script>

<?php require "footer.php"; ?>