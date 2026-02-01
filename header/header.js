// Get the toggle element
const toggle = document.getElementById('theme-toggle');

// Load saved theme from localStorage
if (localStorage.getItem('theme') === 'dark') {
  document.body.classList.add('dark-theme');
  toggle.checked = true;
}

// Listen for toggle changes
toggle.addEventListener('change', () => {
  if (toggle.checked) {
    document.body.classList.add('dark-theme');
    localStorage.setItem('theme', 'dark'); // save preference
  } else {
    document.body.classList.remove('dark-theme');
    localStorage.setItem('theme', 'light'); // save preference
  }
});
