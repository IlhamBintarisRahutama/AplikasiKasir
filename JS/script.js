document.addEventListener("DOMContentLoaded", function () {
  const mobileMenuButton = document.getElementById("menu-toggle-mobile");
  const body = document.body;

  if (mobileMenuButton) {
    mobileMenuButton.addEventListener("click", () => {
      body.classList.toggle("sidebar-open");
      mobileMenuButton.classList.toggle("is-active");
    });
  }

  const dropdowns = document.querySelectorAll(".nav-item-dropdown");

  dropdowns.forEach((dropdown) => {
    const toggle = dropdown.querySelector(".dropdown-toggle");

    toggle.addEventListener("click", () => {
      dropdowns.forEach((otherDropdown) => {
        if (
          otherDropdown !== dropdown &&
          otherDropdown.classList.contains("open")
        ) {
          otherDropdown.classList.remove("open");
          otherDropdown.querySelector(".sub-menu").style.maxHeight = null;
        }
      });

      const subMenu = dropdown.querySelector(".sub-menu");
      dropdown.classList.toggle("open");
      if (dropdown.classList.contains("open")) {
        subMenu.style.maxHeight = subMenu.scrollHeight + "px";
      } else {
        subMenu.style.maxHeight = null;
      }
    });
  });

  const initiallyOpen = document.querySelector(".nav-item-dropdown.open");
  if (initiallyOpen) {
    const subMenu = initiallyOpen.querySelector(".sub-menu");
    if (subMenu) {
      subMenu.style.maxHeight = subMenu.scrollHeight + "px";
    }
  }
});

// Chart.js Configuration
// Chart.js Configuration
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = "#4a5568"; // Default text color for charts

// Sales Report Chart (Bar Chart)
const salesReportCtx = document
  .getElementById("salesReportChart")
  .getContext("2d");
new Chart(salesReportCtx, {
  type: "bar",
  data: {
    labels: [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "Mei",
      "Jun",
      "Jul",
      "Ags",
      "Sep",
      "Okt",
      "Nov",
      "Des",
    ],
    datasets: [
      {
        label: "Penjualan (k)",
        data: [
          4500, 2800, 3900, 2100, 3950, 2850, 4300, 1900, 4400, 5200, 3500,
          5600,
        ],
        backgroundColor: "#f97316" /* orange-500 */,
        borderRadius: 6,
      },
    ],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        grid: {
          color: "#e2e8f0" /* gray-200 */,
          borderDash: [5, 5],
        },
        ticks: {
          callback: function (value) {
            return value / 1000 + "k"; // Format to 2000k, 3000k etc.
          },
        },
      },
      x: {
        grid: {
          display: false,
        },
      },
    },
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        backgroundColor: "#2d3748" /* dark gray */,
        titleColor: "#fff",
        bodyColor: "#fff",
        cornerRadius: 6,
        displayColors: false,
        padding: 12,
        callbacks: {
          label: function (context) {
            return `Penjualan: Rp.${context.formattedValue}.000`;
          },
        },
      },
    },
  },
});
