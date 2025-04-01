document.addEventListener("DOMContentLoaded", () => {
  // Mobile menu toggle
  const menuToggle = document.querySelector(".menu-toggle")
  const mainNav = document.querySelector(".main-nav")

  if (menuToggle && mainNav) {
    menuToggle.addEventListener("click", () => {
      mainNav.classList.toggle("active")
    })
  }

  // Close menu when clicking outside
  document.addEventListener("click", (event) => {
    if (!event.target.closest(".menu-toggle") && !event.target.closest(".main-nav")) {
      if (mainNav && mainNav.classList.contains("active")) {
        mainNav.classList.remove("active")
      }
    }
  })
})

