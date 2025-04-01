document.addEventListener("DOMContentLoaded", () => {
  // Auto-hide alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert")

  if (alerts.length > 0) {
    setTimeout(() => {
      alerts.forEach((alert) => {
        alert.style.opacity = "0"
        setTimeout(() => {
          alert.style.display = "none"
        }, 500)
      })
    }, 5000)
  }

  // Image preview for file inputs
  const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]')

  imageInputs.forEach((input) => {
    input.addEventListener("change", function () {
      const previewContainer = this.parentElement.querySelector(".image-preview")

      if (!previewContainer) return

      if (this.files && this.files[0]) {
        const reader = new FileReader()

        reader.onload = (e) => {
          previewContainer.innerHTML = `<img src="${e.target.result}" style="max-width: 300px; max-height: 200px;">`
        }

        reader.readAsDataURL(this.files[0])
      } else {
        previewContainer.innerHTML = ""
      }
    })
  })
})

