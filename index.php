<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>RMU Student Project Repository</title>
  <link rel="shortcut icon" type="image/png" href="dep_admin/assets/images/logos/rmu.jpg" />
<style>
  *, *::before, *::after { box-sizing: border-box; }

  :root {
    --navy: #002147;
    --navy-light: #003366;
    --accent: #0077b6;
    --accent-soft: rgba(0, 119, 182, 0.08);
    --accent-border: rgba(0, 119, 182, 0.2);
    --bg: #f5f7fa;
    --card-bg: #ffffff;
    --text: #1a2a3a;
    --text-muted: #5a6a7a;
    --border: #e2e8f0;
  }

  body {
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    margin: 0;
    padding: 0;
    color: var(--text);
  }

  /* === HERO HEADER === */
  .hero {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 50%, var(--accent) 100%);
    color: #fff;
    padding: 48px 20px 40px;
    text-align: center;
  }

  .hero-inner {
    max-width: 800px;
    margin: 0 auto;
  }

  .hero img {
    height: 72px;
    margin-bottom: 12px;
    border-radius: 8px;
    background: #fff;
    padding: 4px;
  }

  .hero h1 {
    margin: 0 0 6px;
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.01em;
  }

  .hero p {
    margin: 0;
    font-size: 1.05rem;
    opacity: 0.85;
    font-weight: 400;
    letter-spacing: 0.04em;
  }

  /* === SEARCH SECTION === */
  .search-section {
    max-width: 900px;
    margin: -28px auto 0;
    padding: 0 20px;
    position: relative;
    z-index: 2;
  }

  .search-box {
    background: var(--card-bg);
    border-radius: 14px;
    box-shadow: 0 8px 30px rgba(0, 33, 71, 0.12);
    padding: 24px 28px;
  }

  .search-input-wrap {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
  }

  .search-input-wrap input {
    flex: 1;
    padding: 14px 18px;
    font-size: 16px;
    border: 2px solid var(--border);
    border-radius: 10px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
  }

  .search-input-wrap input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(0, 119, 182, 0.12);
  }

  .search-input-wrap button {
    padding: 14px 28px;
    background: var(--navy);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
    white-space: nowrap;
  }

  .search-input-wrap button:hover {
    background: var(--accent);
  }

  /* === FILTERS === */
  .filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
  }

  .filters select {
    padding: 9px 14px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
    color: var(--text);
    background: #fff;
    outline: none;
    cursor: pointer;
    min-width: 160px;
  }

  .filters select:focus {
    border-color: var(--accent);
  }

  .filters .clear-btn {
    padding: 9px 16px;
    background: none;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: 13px;
    color: var(--text-muted);
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
  }

  .filters .clear-btn:hover {
    border-color: #e74c3c;
    color: #e74c3c;
  }

  /* === STATS BAR === */
  .stats-bar {
    max-width: 1200px;
    margin: 20px auto 0;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .stats-bar .count {
    font-size: 14px;
    color: var(--text-muted);
    font-weight: 600;
  }

  /* === TAG CLOUD === */
  .tag-cloud-section {
    max-width: 900px;
    margin: 20px auto 0;
    padding: 0 20px;
  }

  .tag-cloud-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .tag-cloud-header h3 {
    margin: 0;
    font-size: 14px;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
  }

  .tag-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .tag-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 14px;
    background: var(--card-bg);
    border: 1.5px solid var(--border);
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: var(--navy);
    cursor: pointer;
    transition: all 0.2s;
  }

  .tag-chip:hover, .tag-chip.active {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
  }

  .tag-chip .tag-count {
    font-size: 11px;
    opacity: 0.6;
    font-weight: 400;
  }

  /* === RESULTS === */
  .results-section {
    max-width: 1200px;
    margin: 24px auto 60px;
    padding: 0 20px;
  }

  .results-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  }

  /* === PROJECT CARD === */
  .project-card {
    background: var(--card-bg);
    border-radius: 14px;
    border: none;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    padding: 0;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
  }

  .project-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 33, 71, 0.14);
  }

  /* Card top accent bar */
  .project-card .card-accent {
    height: 5px;
    background: linear-gradient(90deg, var(--navy) 0%, var(--accent) 100%);
  }

  .project-card .card-body-inner {
    padding: 22px 24px 18px;
    display: flex;
    flex-direction: column;
    flex: 1;
  }

  /* Logo watermark */
  .project-card .card-logo {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    object-fit: cover;
    opacity: 0.12;
    pointer-events: none;
  }

  .project-card .card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 14px;
    padding-right: 32px;
  }

  .project-card h3 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--navy);
    line-height: 1.35;
    flex: 1;
    letter-spacing: -0.01em;
  }

  .project-card .year-badge {
    background: var(--navy);
    color: #fff;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
    letter-spacing: 0.04em;
  }

  .project-card .meta {
    font-size: 13px;
    color: var(--text-muted);
    margin: 3px 0;
    line-height: 1.5;
    display: flex;
    align-items: baseline;
    gap: 6px;
  }

  .project-card .meta strong {
    color: var(--navy);
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    min-width: 90px;
    flex-shrink: 0;
  }

  .project-card .synopsis {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.6;
    margin: 12px 0 4px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    padding: 10px 12px;
    background: var(--bg);
    border-radius: 8px;
    border-left: 3px solid var(--accent);
  }

  .project-card .read-more-link {
    display: inline-block;
    font-size: 12px;
    font-weight: 700;
    color: var(--accent);
    cursor: pointer;
    margin-bottom: 4px;
    letter-spacing: 0.02em;
    transition: color 0.2s;
  }

  .project-card .read-more-link:hover {
    color: var(--navy);
    text-decoration: underline;
  }

  /* === DESCRIPTION MODAL === */
  .desc-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    justify-content: center;
    align-items: center;
    padding: 20px;
  }

  .desc-modal-overlay.active {
    display: flex;
  }

  .desc-modal {
    background: var(--card-bg);
    border-radius: 14px;
    width: 100%;
    max-width: 700px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
    overflow: hidden;
  }

  .desc-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    background: var(--navy);
    color: #fff;
  }

  .desc-modal-header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    flex: 1;
    margin-right: 16px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .desc-modal-header .desc-close-btn {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 6px;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .desc-modal-header .desc-close-btn:hover {
    background: rgba(255,255,255,0.3);
  }

  .desc-modal-body {
    padding: 24px 28px;
    overflow-y: auto;
    flex: 1;
    font-size: 14.5px;
    line-height: 1.7;
    color: var(--text);
  }

  .project-card .card-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin: 10px 0;
  }

  .project-card .card-tag {
    display: inline-block;
    background: var(--accent-soft);
    color: var(--navy);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid var(--accent-border);
    transition: all 0.2s;
    letter-spacing: 0.02em;
  }

  .project-card .card-tag:hover {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
  }

  .project-card .card-footer {
    margin-top: auto;
    padding: 14px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(0, 33, 71, 0.02);
  }

  .project-card .dept-label {
    font-size: 11px;
    color: var(--accent);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .project-card .dept-label::before {
    content: '';
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--accent);
    opacity: 0.5;
  }

  .download-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    background: var(--navy);
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    transition: background 0.2s, transform 0.15s;
  }

  .download-btn:hover {
    background: var(--accent);
    transform: translateY(-1px);
  }

  .preview-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s;
  }

  .preview-btn:hover {
    background: var(--navy);
    transform: translateY(-1px);
  }

  /* === PREVIEW MODAL === */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    padding: 20px;
  }

  .modal-overlay.active {
    display: flex;
  }

  .preview-modal {
    background: var(--card-bg);
    border-radius: 14px;
    width: 100%;
    max-width: 900px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
  }

  .preview-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    background: var(--navy);
    color: #fff;
  }

  .preview-modal-header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
    margin-right: 16px;
  }

  .preview-modal-header .modal-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-shrink: 0;
  }

  .preview-modal-header .modal-download-btn {
    padding: 6px 16px;
    background: #fff;
    color: var(--navy);
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    transition: background 0.2s;
  }

  .preview-modal-header .modal-download-btn:hover {
    background: #e2e8f0;
  }

  .preview-modal-header .modal-close-btn {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 6px;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
  }

  .preview-modal-header .modal-close-btn:hover {
    background: rgba(255,255,255,0.3);
  }

  .preview-modal-body {
    flex: 1;
    min-height: 0;
  }

  .preview-modal-body iframe {
    width: 100%;
    height: 70vh;
    border: none;
    display: block;
  }

  .preview-no-file {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 300px;
    color: var(--text-muted);
    font-size: 15px;
    font-style: italic;
  }

  /* === EMPTY / LOADING STATE === */
  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
    grid-column: 1 / -1;
  }

  .empty-state .icon {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.4;
  }

  .empty-state h3 {
    margin: 0 0 8px;
    font-size: 1.1rem;
    color: var(--text);
  }

  .empty-state p {
    margin: 0;
    font-size: 14px;
  }

  .loading {
    text-align: center;
    padding: 40px;
    color: var(--text-muted);
    font-size: 15px;
    grid-column: 1 / -1;
  }

  /* === RESPONSIVE === */
  @media (max-width: 768px) {
    .hero { padding: 36px 16px 32px; }
    .hero h1 { font-size: 1.5rem; }
    .search-section { margin-top: -24px; }
    .search-box { padding: 18px 16px; }
    .search-input-wrap { flex-direction: column; }
    .search-input-wrap button { width: 100%; }
    .filters select { min-width: 0; flex: 1; }
    .results-grid { grid-template-columns: 1fr; }
    .tag-cloud { gap: 6px; }
  }
</style>
</head>
<body>

<!-- HERO -->
<div class="hero">
  <div class="hero-inner">
    <img src="dep_admin/assets/images/logos/rmu.jpg" alt="RMU Logo" />
    <h1>RMU Student Project Repository</h1>
    <p>Search and explore academic projects across all departments</p>
  </div>
</div>

<!-- SEARCH -->
<div class="search-section">
  <div class="search-box">
    <div class="search-input-wrap">
      <input type="text" id="searchInput" placeholder="Search by project title, student name, tag, or keyword..." autocomplete="off" />
      <button onclick="performSearch()">Search</button>
    </div>
    <div class="filters">
      <select id="departmentFilter">
        <option value="">All Departments</option>
      </select>
      <select id="yearFilter">
        <option value="">All Years</option>
      </select>
      <button class="clear-btn" onclick="clearFilters()">Clear All</button>
    </div>
  </div>
</div>

<!-- TAG CLOUD -->
<div class="tag-cloud-section">
  <div class="tag-cloud-header">
    <h3>Browse by Tag</h3>
  </div>
  <div class="tag-cloud" id="tagCloud"></div>
</div>

<!-- STATS -->
<div class="stats-bar">
  <span class="count" id="resultCount"></span>
</div>

<!-- RESULTS -->
<div class="results-section">
  <div class="results-grid" id="results">
    <div class="empty-state">
      <div class="icon">&#128218;</div>
      <h3>Start Searching</h3>
      <p>Enter a keyword above or click a tag to browse projects</p>
    </div>
  </div>
</div>

<script>
let debounceTimer;
let activeTag = '';

// === INIT: Load filters and tag cloud ===
async function loadFilters() {
  try {
    const res = await fetch('get_filters.php');
    const data = await res.json();

    // Department dropdown
    const depSelect = document.getElementById('departmentFilter');
    data.departments.forEach(d => {
      const opt = document.createElement('option');
      opt.value = d.dep_id;
      opt.textContent = d.dep_name;
      depSelect.appendChild(opt);
    });

    // Year dropdown
    const yearSelect = document.getElementById('yearFilter');
    data.years.forEach(y => {
      const opt = document.createElement('option');
      opt.value = y;
      opt.textContent = y;
      yearSelect.appendChild(opt);
    });

    // Tag cloud
    const tagCloud = document.getElementById('tagCloud');
    data.tags.slice(0, 25).forEach(t => {
      const chip = document.createElement('span');
      chip.className = 'tag-chip';
      chip.innerHTML = `${escapeHtml(t.name)} <span class="tag-count">(${t.project_count})</span>`;
      chip.onclick = () => searchByTag(t.name, chip);
      tagCloud.appendChild(chip);
    });

    // Show total
    document.getElementById('resultCount').textContent = data.total_projects + ' projects in the repository';
  } catch (e) {
    console.error('Failed to load filters:', e);
  }
}

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// === SEARCH ===
async function performSearch() {
  const query = document.getElementById('searchInput').value.trim();
  const department = document.getElementById('departmentFilter').value;
  const year = document.getElementById('yearFilter').value;

  const params = new URLSearchParams();
  if (query) params.set('query', query);
  if (department) params.set('department', department);
  if (year) params.set('year', year);
  if (activeTag) params.set('tag', activeTag);

  if (!query && !department && !year && !activeTag) {
    document.getElementById('results').innerHTML = `
      <div class="empty-state">
        <div class="icon">&#128218;</div>
        <h3>Start Searching</h3>
        <p>Enter a keyword above or click a tag to browse projects</p>
      </div>`;
    document.getElementById('resultCount').textContent = '';
    return;
  }

  document.getElementById('results').innerHTML = '<div class="loading">Searching...</div>';

  try {
    const res = await fetch('search_projects.php?' + params.toString());
    const projects = await res.json();

    const resultsDiv = document.getElementById('results');
    const countEl = document.getElementById('resultCount');

    if (projects.length === 0) {
      resultsDiv.innerHTML = `
        <div class="empty-state">
          <div class="icon">&#128269;</div>
          <h3>No Projects Found</h3>
          <p>Try different keywords, or remove some filters</p>
        </div>`;
      countEl.textContent = '0 results';
      return;
    }

    countEl.textContent = projects.length + ' project' + (projects.length !== 1 ? 's' : '') + ' found';
    resultsDiv.innerHTML = projects.map(project => renderCard(project)).join('');

  } catch (error) {
    console.error('Search error:', error);
    document.getElementById('results').innerHTML = `
      <div class="empty-state">
        <div class="icon">&#9888;</div>
        <h3>Something went wrong</h3>
        <p>Please try again</p>
      </div>`;
  }
}

function renderCard(p) {
  const tagsHtml = p.tags
    ? p.tags.split(',').map(tag =>
        `<span class="card-tag" onclick="searchByTagName('${escapeHtml(tag.trim())}')">${escapeHtml(tag.trim())}</span>`
      ).join('')
    : '';

  const fileHtml = (p.file_path && p.file_path.trim())
    ? `<button class="preview-btn" onclick="openPreview('${escapeHtml(p.file_path)}', '${escapeHtml(p.title)}')">&#128196; Preview &amp; Download</button>`
    : `<span style="font-size:12px;color:var(--text-muted)">No file</span>`;

  return `
  <div class="project-card">
    <div class="card-accent"></div>
    <img src="dep_admin/assets/images/logos/rmu.jpg" alt="" class="card-logo" />
    <div class="card-body-inner">
      <div class="card-header">
        <h3>${escapeHtml(p.title)}</h3>
        <span class="year-badge">${escapeHtml(String(p.year))}</span>
      </div>
      <p class="meta"><strong>Students</strong> ${escapeHtml(p.student_names) || 'N/A'}</p>
      <p class="meta"><strong>Supervisor(s)</strong> ${escapeHtml(p.supervisors) || 'N/A'}</p>
      ${p.synopsis ? `<p class="synopsis" data-full-text="${escapeHtml(p.synopsis)}" data-title="${escapeHtml(p.title)}">${escapeHtml(p.synopsis)}</p>${p.synopsis.length > 150 ? `<span class="read-more-link" onclick="openDescFromCard(this)">Read more</span>` : ''}` : ''}
      ${tagsHtml ? `<div class="card-tags">${tagsHtml}</div>` : ''}
    </div>
    <div class="card-footer">
      <span class="dept-label">${escapeHtml(p.dep_name)}</span>
      ${fileHtml}
    </div>
  </div>`;
}

// === TAG SEARCH ===
function searchByTag(tagName, chipEl) {
  // Toggle active state
  document.querySelectorAll('.tag-chip').forEach(c => c.classList.remove('active'));

  if (activeTag === tagName) {
    activeTag = '';
  } else {
    activeTag = tagName;
    if (chipEl) chipEl.classList.add('active');
  }
  performSearch();
}

function searchByTagName(tagName) {
  activeTag = tagName;
  // Highlight matching chip
  document.querySelectorAll('.tag-chip').forEach(c => {
    const chipText = c.textContent.replace(/\s*\(\d+\)\s*$/, '').trim();
    c.classList.toggle('active', chipText === tagName);
  });
  performSearch();
}

function clearFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('departmentFilter').value = '';
  document.getElementById('yearFilter').value = '';
  activeTag = '';
  document.querySelectorAll('.tag-chip').forEach(c => c.classList.remove('active'));
  document.getElementById('results').innerHTML = `
    <div class="empty-state">
      <div class="icon">&#128218;</div>
      <h3>Start Searching</h3>
      <p>Enter a keyword above or click a tag to browse projects</p>
    </div>`;
  document.getElementById('resultCount').textContent = '';
}

// === EVENT LISTENERS ===
document.getElementById('searchInput').addEventListener('input', () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(performSearch, 350);
});

document.getElementById('searchInput').addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    clearTimeout(debounceTimer);
    performSearch();
  }
});

document.getElementById('departmentFilter').addEventListener('change', performSearch);
document.getElementById('yearFilter').addEventListener('change', performSearch);

// Init
loadFilters();
</script>

<!-- PREVIEW MODAL -->
<div class="modal-overlay" id="previewModal">
  <div class="preview-modal">
    <div class="preview-modal-header">
      <h3 id="previewTitle">Project Preview</h3>
      <div class="modal-actions">
        <a id="previewDownloadBtn" href="#" class="modal-download-btn">&#8595; Download</a>
        <button class="modal-close-btn" onclick="closePreview()">&times;</button>
      </div>
    </div>
    <div class="preview-modal-body">
      <iframe id="previewFrame" src=""></iframe>
      <div class="preview-no-file" id="previewNoFile" style="display:none;">No file available for preview.</div>
    </div>
  </div>
</div>

<script>
function openPreview(filePath, title) {
  const modal = document.getElementById('previewModal');
  const frame = document.getElementById('previewFrame');
  const noFile = document.getElementById('previewNoFile');
  const downloadBtn = document.getElementById('previewDownloadBtn');

  document.getElementById('previewTitle').textContent = title || 'Project Preview';

  if (filePath && filePath.trim()) {
    const viewUrl = 'dep_admin/uploads/projects/' + filePath;
    const downloadUrl = 'dep_admin/download.php?file=' + encodeURIComponent(filePath);

    frame.src = viewUrl + '#page=1&zoom=90';
    frame.style.display = 'block';
    noFile.style.display = 'none';
    downloadBtn.href = downloadUrl;
    downloadBtn.style.display = '';
  } else {
    frame.src = '';
    frame.style.display = 'none';
    noFile.style.display = 'flex';
    downloadBtn.style.display = 'none';
  }

  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closePreview() {
  const modal = document.getElementById('previewModal');
  document.getElementById('previewFrame').src = '';
  modal.classList.remove('active');
  document.body.style.overflow = '';
}

// Close on overlay click
document.getElementById('previewModal').addEventListener('click', function(e) {
  if (e.target === this) closePreview();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') { closePreview(); closeDescModal(); }
});
</script>

<!-- DESCRIPTION MODAL -->
<div class="desc-modal-overlay" id="descModal">
  <div class="desc-modal">
    <div class="desc-modal-header">
      <h3 id="descModalTitle">Description</h3>
      <button class="desc-close-btn" onclick="closeDescModal()">&times;</button>
    </div>
    <div class="desc-modal-body" id="descModalBody"></div>
  </div>
</div>

<script>
function openDescFromCard(link) {
  const synopsisEl = link.previousElementSibling;
  const fullText = synopsisEl.getAttribute('data-full-text') || synopsisEl.textContent;
  const title = synopsisEl.getAttribute('data-title') || 'Description';
  document.getElementById('descModalTitle').textContent = title;
  document.getElementById('descModalBody').textContent = fullText;
  document.getElementById('descModal').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeDescModal() {
  document.getElementById('descModal').classList.remove('active');
  if (!document.getElementById('previewModal').classList.contains('active')) {
    document.body.style.overflow = '';
  }
}

document.getElementById('descModal').addEventListener('click', function(e) {
  if (e.target === this) closeDescModal();
});
</script>

</body>
</html>
