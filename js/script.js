// Global variables
let currentUser = null
const isLoading = false

// DOM Content Loaded
document.addEventListener("DOMContentLoaded", () => {
  initializeApp()
  setupEventListeners()
  checkUserSession()
})

// Initialize application
function initializeApp() {
  // Show welcome message
  showWelcomeMessage()

  // Setup smooth scrolling
  setupSmoothScrolling()

  // Setup mobile navigation
  setupMobileNavigation()

  // Setup form validations
  setupFormValidations()
}

// Setup event listeners
function setupEventListeners() {
  // Navigation links
  document.querySelectorAll(".nav-link").forEach((link) => {
    link.addEventListener("click", function (e) {
      if (this.getAttribute("href").startsWith("#")) {
        e.preventDefault()
        const targetId = this.getAttribute("href").substring(1)
        scrollToSection(targetId)
      }
    })
  })

  // Close modals when clicking outside
  window.addEventListener("click", (e) => {
    if (e.target.classList.contains("modal")) {
      closeModal(e.target.id)
    }
  })

  // Form submissions
  setupFormSubmissions()
}

// Show welcome message
function showWelcomeMessage() {
  setTimeout(() => {
    showNotification("Selamat datang di MIKPath! 🎉", "success")
  }, 1000)
}

// Setup smooth scrolling
function setupSmoothScrolling() {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()
      const target = document.querySelector(this.getAttribute("href"))
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        })
      }
    })
  })
}

// Setup mobile navigation
function setupMobileNavigation() {
  const hamburger = document.getElementById("hamburger")
  const navMenu = document.getElementById("navMenu")

  if (hamburger && navMenu) {
    hamburger.addEventListener("click", () => {
      navMenu.classList.toggle("active")
      hamburger.classList.toggle("active")
    })

    // Close menu when clicking on a link
    document.querySelectorAll(".nav-link").forEach((link) => {
      link.addEventListener("click", () => {
        navMenu.classList.remove("active")
        hamburger.classList.remove("active")
      })
    })
  }
}

// Scroll to section
function scrollToSection(sectionId) {
  const section = document.getElementById(sectionId)
  if (section) {
    const navHeight = document.querySelector(".navbar").offsetHeight
    const sectionTop = section.offsetTop - navHeight - 20

    window.scrollTo({
      top: sectionTop,
      behavior: "smooth",
    })
  }
}

// Show notification
function showNotification(message, type = "info", duration = 3000) {
  // Remove existing notifications
  const existingNotifications = document.querySelectorAll(".notification")
  existingNotifications.forEach((notification) => notification.remove())

  // Create notification element
  const notification = document.createElement("div")
  notification.className = `notification alert alert-${type}`
  notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 3000;
        min-width: 300px;
        max-width: 400px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `

  // Add icon based on type
  const icons = {
    success: "✅",
    error: "❌",
    warning: "⚠️",
    info: "ℹ️",
  }

  notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 1.2rem;">${icons[type] || icons.info}</span>
            <span>${message}</span>
        </div>
    `

  document.body.appendChild(notification)

  // Animate in
  setTimeout(() => {
    notification.style.transform = "translateX(0)"
  }, 100)

  // Auto remove
  setTimeout(() => {
    notification.style.transform = "translateX(100%)"
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification)
      }
    }, 300)
  }, duration)
}

// Show confirmation dialog
function showConfirmation(message, onConfirm, onCancel = null) {
  const confirmed = confirm(message)
  if (confirmed && onConfirm) {
    onConfirm()
  } else if (!confirmed && onCancel) {
    onCancel()
  }
  return confirmed
}

// Modal functions
function showModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.style.display = "block"
    document.body.style.overflow = "hidden"

    // Focus first input
    const firstInput = modal.querySelector("input, textarea, select")
    if (firstInput) {
      setTimeout(() => firstInput.focus(), 100)
    }
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.style.display = "none"
    document.body.style.overflow = "auto"

    // Reset form if exists
    const form = modal.querySelector("form")
    if (form) {
      form.reset()
    }
  }
}

// Career management functions
function showAddCareerModal() {
  showModal("addCareerModal")
}

function editCareer(careerId) {
  showConfirmation("Apakah Anda yakin ingin mengedit data karier ini?", () => {
    // Load career data and show edit modal
    loadCareerData(careerId)
    showModal("editCareerModal")
  })
}

function deleteCareer(careerId) {
  showConfirmation("Apakah Anda yakin ingin menghapus data karier ini? Tindakan ini tidak dapat dibatalkan!", () => {
    // Show loading
    showNotification("Menghapus data...", "info")

    // Simulate API call
    setTimeout(() => {
      // In real implementation, make AJAX call to delete
      fetch("api/delete_career.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: careerId }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showNotification("Data karier berhasil dihapus!", "success")
            // Reload page or remove element
            setTimeout(() => location.reload(), 1000)
          } else {
            showNotification("Gagal menghapus data: " + data.message, "error")
          }
        })
        .catch((error) => {
          showNotification("Terjadi kesalahan saat menghapus data", "error")
        })
    }, 500)
  })
}

function loadCareerData(careerId) {
  // In real implementation, fetch career data from server
  fetch(`api/get_career.php?id=${careerId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Populate edit form with data
        populateEditForm(data.career)
      }
    })
    .catch((error) => {
      showNotification("Gagal memuat data karier", "error")
    })
}

function populateEditForm(careerData) {
  // Populate form fields with career data
  const form = document.getElementById("editCareerForm")
  if (form) {
    Object.keys(careerData).forEach((key) => {
      const field = form.querySelector(`[name="${key}"]`)
      if (field) {
        field.value = careerData[key]
      }
    })
  }
}

// Alumni story functions
function showAddStoryModal() {
  showModal("addStoryModal")
}

// Form validation and submission
function setupFormValidations() {
  // Add real-time validation for forms
  document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!validateForm(this)) {
        e.preventDefault()
        showNotification("Mohon lengkapi semua field yang diperlukan", "warning")
      }
    })
  })
}

function validateForm(form) {
  let isValid = true
  const requiredFields = form.querySelectorAll("[required]")

  requiredFields.forEach((field) => {
    if (!field.value.trim()) {
      field.style.borderColor = "#ef4444"
      isValid = false
    } else {
      field.style.borderColor = "#e2e8f0"
    }
  })

  return isValid
}

function setupFormSubmissions() {
  // Career form submission
  const addCareerForm = document.getElementById("addCareerForm")
  if (addCareerForm) {
    addCareerForm.addEventListener("submit", function (e) {
      e.preventDefault()
      submitCareerForm(this)
    })
  }

  // Alumni story form submission
  const addStoryForm = document.getElementById("addStoryForm")
  if (addStoryForm) {
    addStoryForm.addEventListener("submit", function (e) {
      e.preventDefault()
      submitStoryForm(this)
    })
  }
}

function submitCareerForm(form) {
  if (!validateForm(form)) return

  const formData = new FormData(form)
  const submitBtn = form.querySelector('button[type="submit"]')

  // Show loading state
  submitBtn.innerHTML = '<span class="loading"></span> Menyimpan...'
  submitBtn.disabled = true

  fetch("api/add_career.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification("Data karier berhasil ditambahkan!", "success")
        closeModal("addCareerModal")
        setTimeout(() => location.reload(), 1000)
      } else {
        showNotification("Gagal menambahkan data: " + data.message, "error")
      }
    })
    .catch((error) => {
      showNotification("Terjadi kesalahan saat menyimpan data", "error")
    })
    .finally(() => {
      submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan'
      submitBtn.disabled = false
    })
}

function submitStoryForm(form) {
  if (!validateForm(form)) return

  const formData = new FormData(form)
  const submitBtn = form.querySelector('button[type="submit"]')

  // Show loading state
  submitBtn.innerHTML = '<span class="loading"></span> Mengirim...'
  submitBtn.disabled = true

  fetch("api/add_story.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification("Cerita berhasil dikirim! 🎉", "success")
        closeModal("addStoryModal")

        // Add the new story to the alumni grid
        if (data.story) {
          addAlumniStory(data.story)
        }

        // Scroll to alumni section
        setTimeout(() => {
          document.getElementById("alumni").scrollIntoView({ behavior: "smooth" })
        }, 1000)
      } else {
        showNotification("Gagal mengirim cerita: " + data.message, "error")
      }
    })
    .catch((error) => {
      showNotification("Terjadi kesalahan saat mengirim cerita", "error")
    })
    .finally(() => {
      submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Cerita'
      submitBtn.disabled = false
    })
}

function addAlumniStory(story) {
  const alumniGrid = document.querySelector(".alumni-grid")
  if (!alumniGrid) return

  const storyCard = createAlumniCard(story)
  alumniGrid.appendChild(storyCard)
}

// User session management
function checkUserSession() {
  // Check if user is logged in
  fetch("api/check_session.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.logged_in) {
        currentUser = data.user
        updateUIForLoggedInUser()
      }
    })
    .catch((error) => {
      console.log("Session check failed")
    })
}

function updateUIForLoggedInUser() {
  if (currentUser) {
    // Update navigation or other UI elements
    const userGreeting = document.querySelector(".user-greeting")
    if (userGreeting) {
      userGreeting.textContent = `Halo, ${currentUser.username}!`
    }
  }
}

// Image preview functionality
function previewImage(input, previewId) {
  const file = input.files[0]
  const preview = document.getElementById(previewId)

  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      preview.src = e.target.result
      preview.style.display = "block"
    }
    reader.readAsDataURL(file)
  } else {
    preview.style.display = "none"
  }
}

// Search and filter functionality
function filterCareers(searchTerm) {
  const careerCards = document.querySelectorAll(".course-card")

  careerCards.forEach((card) => {
    const text = card.textContent.toLowerCase()
    if (text.includes(searchTerm.toLowerCase())) {
      card.style.display = "block"
    } else {
      card.style.display = "none"
    }
  })
}

function filterAlumni(searchTerm) {
  const alumniCards = document.querySelectorAll(".alumni-card")

  alumniCards.forEach((card) => {
    const text = card.textContent.toLowerCase()
    if (text.includes(searchTerm.toLowerCase())) {
      card.style.display = "block"
    } else {
      card.style.display = "none"
    }
  })
}

// Utility functions
function formatDate(dateString) {
  const options = {
    year: "numeric",
    month: "long",
    day: "numeric",
  }
  return new Date(dateString).toLocaleDateString("id-ID", options)
}

function truncateText(text, maxLength) {
  if (text.length <= maxLength) return text
  return text.substr(0, maxLength) + "..."
}

// Export functions for global access
window.scrollToSection = scrollToSection
window.showModal = showModal
window.closeModal = closeModal
window.showAddCareerModal = showAddCareerModal
window.editCareer = editCareer
window.deleteCareer = deleteCareer
window.showAddStoryModal = showAddStoryModal
window.previewImage = previewImage
window.filterCareers = filterCareers
window.filterAlumni = filterAlumni

// Console welcome message
console.log("%c🎉 Welcome to MIKPath!", "color: #6366f1; font-size: 20px; font-weight: bold;")
console.log("%cPeta Karier Manajemen Informasi Kesehatan", "color: #8b5cf6; font-size: 14px;")

function createAlumniCard(alumni) {
  const card = document.createElement("div")
  card.className = "alumni-card"

  card.innerHTML = `
    <div class="alumni-header">
      <div class="alumni-avatar">
        ${alumni.image ? `<img src="uploads/${alumni.image}" alt="Alumni">` : '<i class="fas fa-user"></i>'}
      </div>
      <div class="alumni-info">
        <h4>${alumni.alumni_name}</h4>
        <span class="graduation-year">Lulusan ${alumni.graduation_year}</span>
      </div>
    </div>
    <div class="alumni-body">
      <div class="institution">
        <i class="fas fa-building"></i>
        <span>${alumni.institution}</span>
      </div>
      <div class="position">
        <i class="fas fa-briefcase"></i>
        <span>${alumni.position}</span>
      </div>
      <div class="story">
        <p>${alumni.story}</p>
      </div>
      ${
        alumni.job_opportunities
          ? `
        <div class="job-opportunities">
          <strong><i class="fas fa-search"></i> Lowongan Terkait:</strong>
          <p>${alumni.job_opportunities}</p>
        </div>
      `
          : ""
      }
    </div>
  `

  return card
}
