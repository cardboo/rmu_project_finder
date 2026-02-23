<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Project Repository Search</title>
   <link rel="shortcut icon" type="image/png" href="../assets/images/logos/rmu.jpg" />
<style>
  :root {
    --sea-blue-dark: #004d66;
    --sea-blue: #007a99;
    --sea-blue-soft: rgba(0, 77, 102, 0.08);
    --sea-blue-border: rgba(0, 77, 102, 0.18);
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #ffffff;
    padding: 30px 10px;
    color: var(--sea-blue-dark);
    text-align: center;
  }

  /* HEADER */
  header {
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
  }

  header img {
    height: 60px;
    object-fit: contain;
  }

  h1 {
    margin: 0;
    color: var(--sea-blue-dark);
    font-weight: 800;
    font-size: 2.4rem;
    letter-spacing: -0.02em;
  }

  p.subtitle {
    margin: 4px 0 0;
    color: var(--sea-blue);
    font-size: 1.05rem;
    font-weight: 500;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  /* SEARCH INPUT */
  #searchInput {
    width: 80%;
    max-width: 600px;
    padding: 15px 18px;
    font-size: 17px;
    border-radius: 8px;
    border: 2px solid var(--sea-blue-border);
    box-shadow: 0 4px 12px rgba(0, 77, 102, 0.12);
    outline: none;
    transition: box-shadow 0.25s ease, border-color 0.25s ease;
  }

  #searchInput:focus {
    border-color: var(--sea-blue);
    box-shadow: 0 6px 20px rgba(0, 122, 153, 0.25);
  }

  /* RESULTS GRID */
  #results {
    margin-top: 30px;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
    display: grid;
    gap: 24px;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    text-align: left;
  }

  /* PROJECT CARD */
  .project-card {
    background: #ffffff;
    padding: 22px 26px;
    border-radius: 12px;
    border-top: 5px solid var(--sea-blue-dark);
    box-shadow: 0 8px 22px rgba(0, 77, 102, 0.12);
    color: var(--sea-blue-dark);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .project-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 32px rgba(0, 77, 102, 0.18);
  }

  .project-card h3 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--sea-blue-dark);
    line-height: 1.3;
  }

  .project-card p {
    margin: 6px 0;
    font-size: 14.5px;
    color: #335f6f;
    line-height: 1.55;
  }

  .project-card p strong {
    color: var(--sea-blue-dark);
    font-weight: 600;
  }

  /* TAGS */
  .tag {
    display: inline-block;
    background-color: var(--sea-blue-soft);
    color: var(--sea-blue-dark);
    padding: 4px 10px;
    margin: 4px 6px 0 0;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid var(--sea-blue-border);
  }

  /* DOWNLOAD BUTTON */
  .download-btn {
    display: inline-block;
    background-color: var(--sea-blue-dark);
    color: #ffffff;
    padding: 7px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    transition: background-color 0.25s ease, transform 0.15s ease;
  }

  .download-btn:hover {
    background-color: var(--sea-blue);
    transform: translateY(-1px);
  }

  /* NO RESULTS */
  #no-results {
    color: var(--sea-blue);
    font-style: italic;
    margin-top: 25px;
    text-align: center;
    font-size: 15px;
  }
</style>

</head>
<body>

  <header>
    <!-- Replace 'school-logo.png' with your actual logo path -->
    <img src="dep_admin/assets/images/logos/rmu.jpg" alt="School Logo" />
    <div>
      <h1> RMU Student Project Repository</h1>

      <p class="subtitle">Search and explore academic projects</p>
    </div>
  </header>

 <div style="margin: 0 auto; max-width: 600px; display: flex; gap: 8px; justify-content: center;">
  <input
    type="text"
    id="searchInput"
    placeholder="Search by project title, student, or tag..."
    autocomplete="off"
    style="flex-grow: 1; padding: 15px; font-size: 18px; border-radius: 8px; border: 2px solid #004d66;"
  />


</div>

  <div id="results"></div>

<script>
let debounceTimer;

async function searchProjects(query) {
  const resultsDiv = document.getElementById('results');
  resultsDiv.innerHTML = '';

  if (!query) return;

  try {
    const response = await fetch(`search_projects.php?query=${encodeURIComponent(query)}`);
    const projects = await response.json();

    if (projects.length === 0) {
      resultsDiv.innerHTML = '<p id="no-results">No projects found.</p>';
      return;
    }

    projects.forEach(project => {
      const tagsHtml = project.tags
        ? project.tags.split(',').map(tag => `<span class="tag">${tag.trim()}</span>`).join(' ')
        : '';

      const supervisorsHtml = project.supervisors ? project.supervisors : 'N/A';

   

     const projectHtml = `
<div class="project-card">
  <h3>${project.title}</h3>
  <p><strong>Department:</strong> ${project.dep_name}</p>
  <p><strong>Year:</strong> ${project.year}</p>
  <p><strong>Students:</strong> ${project.student_names}</p>
  <p><strong>Tags:</strong> ${tagsHtml}</p>
  <p><strong>Synopsis:</strong> ${project.synopsis}</p>
  <p><strong>Project Abstract:</strong> 
   <a href="dep_admin/download.php?file=${project.file_path}"
   class="download-btn">
   Download
</a>


  </p>
</div>`;


      resultsDiv.insertAdjacentHTML('beforeend', projectHtml);
    });
  } catch (error) {
    console.error('Search error:', error);
    resultsDiv.innerHTML = '<p style="color:red;">Something went wrong.</p>';
  }
}

// Live search with debounce
document.getElementById('searchInput').addEventListener('input', (e) => {
  clearTimeout(debounceTimer);
  const query = e.target.value.trim();
  debounceTimer = setTimeout(() => searchProjects(query), 300); // 300ms delay
});

function highlightMatch(text, query) {
  if (!query) return text;
  // Escape special regex characters in query
  const escapedQuery = query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
  const regex = new RegExp(`(${escapedQuery.split(' ').join('|')})`, 'gi');
  return text.replace(regex, '<mark>$1</mark>');
}

</script>



</body>
</html>
