document.addEventListener("DOMContentLoaded", function () {
  const comparisonBody = document.getElementById("comparison-body");
  const columns = Array.from(
    comparisonBody.querySelectorAll('.column:not([data-column="features"])')
  );
  const addColumnBtn = document.getElementById("add-column");
  const resetColumnsBtn = document.getElementById("reset-columns");
  const scrollHint = document.getElementById("scroll-hint");

  let draggedColumn = null;
  let originalOrder = columns.map((col) => col.getAttribute("data-column"));
  let dragIndicator = document.createElement("div");
  dragIndicator.className = "drag-indicator";
  document.body.appendChild(dragIndicator);

  // Touch event variables
  let touchStartX = 0;
  let touchStartY = 0;
  let isTouchDragging = false;
  let touchDragTimeout = null;
  let scrollInterval = null;

  // Auto-scroll function
  function startAutoScroll(direction) {
    if (scrollInterval) clearInterval(scrollInterval);

    scrollInterval = setInterval(() => {
      const scrollAmount = direction === "right" ? 50 : -50;
      comparisonBody.scrollLeft += scrollAmount;

      // Show scroll hint if needed
      if (
        direction === "right" &&
        comparisonBody.scrollLeft <
          comparisonBody.scrollWidth - comparisonBody.clientWidth - 10
      ) {
        scrollHint.style.display = "block";
        scrollHint.textContent = "Scroll →";
      } else if (direction === "left" && comparisonBody.scrollLeft > 10) {
        scrollHint.style.display = "block";
        scrollHint.textContent = "← Scroll";
      } else {
        scrollHint.style.display = "none";
      }
    }, 50);
  }

  function stopAutoScroll() {
    if (scrollInterval) {
      clearInterval(scrollInterval);
      scrollInterval = null;
    }
    scrollHint.style.display = "none";
  }

  // Initialize drag events for column headers
  function initDragEvents() {
    document
      .querySelectorAll('.column-header[draggable="true"]')
      .forEach((header) => {
        // Desktop events
        header.addEventListener("dragstart", function (e) {
          draggedColumn = this.parentElement;
          draggedColumn.classList.add("dragging");

          // Set drag image to an empty image for smoother visual
          const dragImage = new Image();
          e.dataTransfer.setDragImage(dragImage, 0, 0);
          e.dataTransfer.effectAllowed = "move";
        });

        header.addEventListener("drag", function (e) {
          // Update indicator position during drag
          if (draggedColumn) {
            const rect = draggedColumn.getBoundingClientRect();
            const scrollX =
              window.pageXOffset || document.documentElement.scrollLeft;
            dragIndicator.style.left = rect.left + scrollX - 2 + "px";
            dragIndicator.style.top = rect.top + "px";
            dragIndicator.style.height = rect.height + "px";
            dragIndicator.style.display = "block";
          }
        });

        header.addEventListener("dragend", function () {
          document.querySelectorAll(".column").forEach((col) => {
            col.classList.remove("drop-before", "drop-after", "drop-target");
          });

          if (draggedColumn) {
            draggedColumn.classList.remove("dragging");
            draggedColumn = null;
          }
          dragIndicator.style.display = "none";
          stopAutoScroll();
        });

        // Mobile touch events
        header.addEventListener("touchstart", function (e) {
          if (isTouchDragging) return;

          const touch = e.touches[0];
          touchStartX = touch.clientX;
          touchStartY = touch.clientY;

          // Set a timeout to distinguish between tap and drag
          touchDragTimeout = setTimeout(() => {
            isTouchDragging = true;
            draggedColumn = this.parentElement;
            draggedColumn.classList.add("dragging");

            // Prevent default to avoid scrolling while dragging
            e.preventDefault();
          }, 300);
        });

        header.addEventListener("touchmove", function (e) {
          if (!isTouchDragging || !draggedColumn) return;

          const touch = e.touches[0];
          const deltaX = touch.clientX - touchStartX;
          const deltaY = touch.clientY - touchStartY;

          // Only start dragging if movement is primarily horizontal
          if (Math.abs(deltaX) > 10 && Math.abs(deltaX) > Math.abs(deltaY)) {
            e.preventDefault();

            // Update dragged column position
            const rect = draggedColumn.getBoundingClientRect();
            const scrollX =
              window.pageXOffset || document.documentElement.scrollLeft;
            dragIndicator.style.left = rect.left + scrollX - 2 + "px";
            dragIndicator.style.top = rect.top + "px";
            dragIndicator.style.height = rect.height + "px";
            dragIndicator.style.display = "block";

            // Auto-scroll logic
            const scrollThreshold = 50; // pixels from edge
            const clientX = touch.clientX;
            const windowWidth = window.innerWidth;

            if (clientX > windowWidth - scrollThreshold) {
              startAutoScroll("right");
            } else if (clientX < scrollThreshold) {
              startAutoScroll("left");
            } else {
              stopAutoScroll();
            }

            // Find which column we're over
            const overElement = document.elementFromPoint(
              touch.clientX,
              touch.clientY
            );
            const overColumn = overElement
              ? overElement.closest(".column")
              : null;

            if (
              overColumn &&
              overColumn !== draggedColumn &&
              overColumn.getAttribute("data-column") !== "features"
            ) {
              // Clear all drop classes first
              document.querySelectorAll(".column").forEach((col) => {
                col.classList.remove(
                  "drop-before",
                  "drop-after",
                  "drop-target"
                );
              });

              // Highlight the column being dragged over
              overColumn.classList.add("drop-target");

              // Determine drop position
              const rect = overColumn.getBoundingClientRect();
              const x = touch.clientX - rect.left;
              const width = rect.width;

              if (x < width / 2) {
                overColumn.classList.add("drop-before");
              } else {
                overColumn.classList.add("drop-after");
              }
            }
          }
        });

        header.addEventListener("touchend", function (e) {
          clearTimeout(touchDragTimeout);
          stopAutoScroll();

          if (!isTouchDragging) return;
          isTouchDragging = false;

          // Find which column we're over
          const touch = e.changedTouches[0];
          const overElement = document.elementFromPoint(
            touch.clientX,
            touch.clientY
          );
          const overColumn = overElement
            ? overElement.closest(".column")
            : null;

          if (
            overColumn &&
            overColumn !== draggedColumn &&
            overColumn.getAttribute("data-column") !== "features"
          ) {
            // Determine drop position
            const rect = overColumn.getBoundingClientRect();
            const x = touch.clientX - rect.left;
            const width = rect.width;

            // Move the dragged column
            if (x < width / 2) {
              // Drop before this column
              comparisonBody.insertBefore(draggedColumn, overColumn);
            } else {
              // Drop after this column
              comparisonBody.insertBefore(
                draggedColumn,
                overColumn.nextSibling
              );
            }
          }

          // Clear drop classes
          document.querySelectorAll(".column").forEach((col) => {
            col.classList.remove("drop-before", "drop-after", "drop-target");
          });

          if (draggedColumn) {
            draggedColumn.classList.remove("dragging");
            draggedColumn = null;
          }
          dragIndicator.style.display = "none";
        });

        // Prevent context menu on long press on mobile
        header.addEventListener("contextmenu", function (e) {
          e.preventDefault();
        });
      });

    // Add event listeners to columns for drop targeting (desktop only)
    document.querySelectorAll(".column").forEach((column) => {
      // Skip the features column
      if (column.getAttribute("data-column") === "features") return;

      column.addEventListener("dragover", function (e) {
        e.preventDefault();

        if (!draggedColumn || column === draggedColumn) {
          return;
        }

        const rect = column.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const width = rect.width;

        // Clear all drop classes first
        document.querySelectorAll(".column").forEach((col) => {
          col.classList.remove("drop-before", "drop-after", "drop-target");
        });

        // Highlight the column being dragged over
        column.classList.add("drop-target");

        // Determine drop position (before or after the column)
        if (x < width / 2) {
          // Drop before this column
          column.classList.add("drop-before");
        } else {
          // Drop after this column
          column.classList.add("drop-after");
        }

        // Auto-scroll logic for desktop
        const scrollThreshold = 50; // pixels from edge
        const clientX = e.clientX;
        const windowWidth = window.innerWidth;

        if (clientX > windowWidth - scrollThreshold) {
          startAutoScroll("right");
        } else if (clientX < scrollThreshold) {
          startAutoScroll("left");
        } else {
          stopAutoScroll();
        }
      });

      column.addEventListener("dragleave", function () {
        this.classList.remove("drop-before", "drop-after", "drop-target");
        stopAutoScroll();
      });

      column.addEventListener("drop", function (e) {
        e.preventDefault();
        stopAutoScroll();

        if (!draggedColumn || draggedColumn === this) return;

        const allColumns = Array.from(
          comparisonBody.querySelectorAll(
            '.column:not([data-column="features"])'
          )
        );
        const targetCol = this;

        // Determine drop position
        const rect = targetCol.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const width = rect.width;

        // Move the dragged column
        if (x < width / 2) {
          // Drop before this column
          comparisonBody.insertBefore(draggedColumn, targetCol);
        } else {
          // Drop after this column
          comparisonBody.insertBefore(draggedColumn, targetCol.nextSibling);
        }

        // Clear drop classes
        document.querySelectorAll(".column").forEach((col) => {
          col.classList.remove("drop-before", "drop-after", "drop-target");
        });
      });
    });
  }

  // Add new column
  addColumnBtn.addEventListener("click", function () {
    const newColumnNumber =
      comparisonBody.querySelectorAll('.column:not([data-column="features"])')
        .length + 1;
    const newColumn = document.createElement("div");
    newColumn.className = "column";
    newColumn.setAttribute("data-column", newColumnNumber);

    const randomPrice = Math.floor(Math.random() * 300) + 100;
    const randomRating = (Math.random() * 1.5 + 3.5).toFixed(1);
    const randomWarranty = Math.floor(Math.random() * 5) + 1;
    const randomWeight = (Math.random() * 1.5 + 0.5).toFixed(1);
    const randomDim1 = Math.floor(Math.random() * 10) + 5;
    const randomDim2 = Math.floor(Math.random() * 10) + 10;
    const randomDim3 = Math.floor(Math.random() * 5) + 3;

    newColumn.innerHTML = `
                    <div class="column-header" draggable="true">Product ${String.fromCharCode(
                      64 + newColumnNumber
                    )}</div>
                    <div class="column-content">
                        <div class="feature">$${randomPrice}</div>
                        <div class="feature">${randomRating}/5</div>
                        <div class="feature">${randomWarranty} year${
      randomWarranty > 1 ? "s" : ""
    }</div>
                        <div class="feature">${randomWeight} kg</div>
                        <div class="feature">${randomDim1}x${randomDim2}x${randomDim3} cm</div>
                    </div>
                `;

    comparisonBody.appendChild(newColumn);
    initDragEvents();
  });

  // Reset columns to original order
  resetColumnsBtn.addEventListener("click", function () {
    const featuresCol = comparisonBody.querySelector(
      '[data-column="features"]'
    );
    const currentColumns = Array.from(
      comparisonBody.querySelectorAll('.column:not([data-column="features"])')
    );

    // Clear current columns
    currentColumns.forEach((col) => col.remove());

    // Recreate columns in original order
    originalOrder.forEach((colNum) => {
      const col = document.querySelector(`[data-column="${colNum}"]`);
      if (col) {
        comparisonBody.appendChild(col);
      }
    });

    initDragEvents();
  });

  // Initialize everything
  initDragEvents();
});
