<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Draggable Comparison Tool with Auto-Scroll</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            touch-action: manipulation;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            color: #2c3e50;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .description {
            font-size: 1.1rem;
            color: #34495e;
            max-width: 800px;
            margin: 0 auto;
        }

        .comparison-tool {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
            width: 100%;
        }

        .tool-header {
            display: flex;
            background: #3498db;
            color: white;
        }

        .header-cell {
            padding: 20px;
            flex: 1;
            min-width: 150px;
            text-align: center;
            font-weight: bold;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-cell:last-child {
            border-right: none;
        }

        .tool-body {
            display: flex;
            position: relative;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        .column {
            flex: 1;
            min-width: 150px;
            border-right: 1px solid #eaeaea;
            transition: all 0.3s ease;
        }

        .column:last-child {
            border-right: none;
        }

        .column.dragging {
            opacity: 0.7;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            z-index: 10;
            transform: scale(1.02);
        }

        .column.drop-target {
            background-color: #f0f0f0 !important;
            transition: background-color 0.2s ease;
        }

        .column.drop-before::before,
        .column.drop-after::after {
            content: '';
            position: absolute;
            height: 100%;
            width: 4px;
            background: #3498db;
            z-index: 10;
            top: 0;
        }

        .column.drop-before::before {
            left: -2px;
        }

        .column.drop-after::after {
            right: -2px;
        }

        .column-header {
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            font-weight: 600;
            cursor: grab;
            user-select: none;
            transition: background 0.2s;
            touch-action: none;
        }

        .column-header:hover {
            background: #e9ecef;
        }

        .column-header:active {
            cursor: grabbing;
        }

        .column-content {
            padding: 15px;
            min-height: 200px;
        }

        .feature {
            padding: 10px;
            border-bottom: 1px solid #f1f1f1;
            text-align: center;
        }

        .feature:last-child {
            border-bottom: none;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        button {
            padding: 12px 25px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .instructions {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
            max-width: 800px;
        }

        .instructions h2 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .instructions ul {
            list-style-position: inside;
            line-height: 1.6;
            color: #34495e;
        }

        .instructions li {
            margin-bottom: 10px;
        }

        .drag-indicator {
            position: absolute;
            height: 100%;
            width: 4px;
            background: #3498db;
            z-index: 10;
            pointer-events: none;
            display: none;
        }

        .mobile-hint {
            display: none;
            text-align: center;
            padding: 10px;
            background: #fff3cd;
            color: #856404;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            .tool-body {
                overflow-x: auto;
            }

            .column {
                min-width: 150px;
            }

            .header-cell {
                min-width: 150px;
                padding: 15px 10px;
            }

            .mobile-hint {
                display: block;
            }

            .controls {
                flex-direction: column;
                align-items: center;
            }

            button {
                width: 100%;
                max-width: 300px;
            }
        }

        @media (max-width: 480px) {

            .header-cell,
            .column {
                min-width: 130px;
            }

            .column-header {
                padding: 12px 8px;
            }

            .column-content {
                padding: 10px;
            }

            .feature {
                padding: 8px 5px;
                font-size: 0.9rem;
            }
        }

        .scroll-hint {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            z-index: 20;
            display: none;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.7;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>Mobile Draggable Comparison Tool</h1>
            <p class="description">Drag and drop columns to rearrange them. Auto-scrolls when dragging near edges.</p>
        </header>

        <div class="mobile-hint">
            💡 Tap and hold to drag columns. The table will automatically scroll when dragging near edges.
        </div>

        <div class="comparison-tool">
            <div class="tool-header">
                <div class="header-cell">Features</div>
                <div class="header-cell">Product A</div>
                <div class="header-cell">Product B</div>
                <div class="header-cell">Product C</div>
                <div class="header-cell">Product D</div>
                <div class="header-cell">Product E</div>
                <div class="header-cell">Product F</div>
            </div>

            <div class="tool-body" id="comparison-body">
                <!-- Scroll hint element -->
                <div class="scroll-hint" id="scroll-hint">Scroll →</div>

                <!-- Features column -->
                <div class="column" data-column="features">
                    <div class="column-header">Features</div>
                    <div class="column-content">
                        <div class="feature">Price</div>
                        <div class="feature">Rating</div>
                        <div class="feature">Warranty</div>
                        <div class="feature">Weight</div>
                        <div class="feature">Dimensions</div>
                    </div>
                </div>

                <!-- Draggable columns -->
                <div class="column" data-column="1">
                    <div class="column-header" draggable="true">Product A</div>
                    <div class="column-content">
                        <div class="feature">$199</div>
                        <div class="feature">4.5/5</div>
                        <div class="feature">2 years</div>
                        <div class="feature">1.2 kg</div>
                        <div class="feature">10x15x5 cm</div>
                    </div>
                </div>

                <div class="column" data-column="2">
                    <div class="column-header" draggable="true">Product B</div>
                    <div class="column-content">
                        <div class="feature">$249</div>
                        <div class="feature">4.7/5</div>
                        <div class="feature">3 years</div>
                        <div class="feature">1.5 kg</div>
                        <div class="feature">12x18x6 cm</div>
                    </div>
                </div>

                <div class="column" data-column="3">
                    <div class="column-header" draggable="true">Product C</div>
                    <div class="column-content">
                        <div class="feature">$179</div>
                        <div class="feature">4.2/5</div>
                        <div class="feature">1 year</div>
                        <div class="feature">0.9 kg</div>
                        <div class="feature">9x14x4 cm</div>
                    </div>
                </div>

                <div class="column" data-column="4">
                    <div class="column-header" draggable="true">Product D</div>
                    <div class="column-content">
                        <div class="feature">$299</div>
                        <div class="feature">4.9/5</div>
                        <div class="feature">5 years</div>
                        <div class="feature">1.8 kg</div>
                        <div class="feature">15x20x7 cm</div>
                    </div>
                </div>

                <div class="column" data-column="5">
                    <div class="column-header" draggable="true">Product E</div>
                    <div class="column-content">
                        <div class="feature">$349</div>
                        <div class="feature">4.8/5</div>
                        <div class="feature">4 years</div>
                        <div class="feature">2.1 kg</div>
                        <div class="feature">16x22x8 cm</div>
                    </div>
                </div>

                <div class="column" data-column="6">
                    <div class="column-header" draggable="true">Product F</div>
                    <div class="column-content">
                        <div class="feature">$399</div>
                        <div class="feature">5.0/5</div>
                        <div class="feature">6 years</div>
                        <div class="feature">2.5 kg</div>
                        <div class="feature">18x25x9 cm</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="controls">
            <button id="add-column">Add Column</button>
            <button id="reset-columns">Reset Order</button>
        </div>

        <div class="instructions">
            <h2>How to Use This Tool</h2>
            <ul>
                <li><strong>On desktop:</strong> Click and drag the column header to move columns</li>
                <li><strong>On mobile:</strong> Tap and hold on a column header, then drag to move it</li>
                <li>The table will automatically scroll when dragging near the edges</li>
                <li>Drag over other columns to see the drop indicator</li>
                <li>The column being dragged over turns light gray</li>
                <li>Release to drop the column in the new position</li>
                <li>Add new columns with the "Add Column" button</li>
                <li>Reset to the original order with the "Reset Order" button</li>
            </ul>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const comparisonBody = document.getElementById('comparison-body');
            const columns = Array.from(comparisonBody.querySelectorAll('.column:not([data-column="features"])'));
            const addColumnBtn = document.getElementById('add-column');
            const resetColumnsBtn = document.getElementById('reset-columns');
            const scrollHint = document.getElementById('scroll-hint');

            let draggedColumn = null;
            let originalOrder = columns.map(col => col.getAttribute('data-column'));
            let dragIndicator = document.createElement('div');
            dragIndicator.className = 'drag-indicator';
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
                    const scrollAmount = direction === 'right' ? 50 : -50;
                    comparisonBody.scrollLeft += scrollAmount;

                    // Show scroll hint if needed
                    if (direction === 'right' &&
                        comparisonBody.scrollLeft < (comparisonBody.scrollWidth - comparisonBody.clientWidth - 10)) {
                        scrollHint.style.display = 'block';
                        scrollHint.textContent = 'Scroll →';
                    } else if (direction === 'left' && comparisonBody.scrollLeft > 10) {
                        scrollHint.style.display = 'block';
                        scrollHint.textContent = '← Scroll';
                    } else {
                        scrollHint.style.display = 'none';
                    }
                }, 50);
            }

            function stopAutoScroll() {
                if (scrollInterval) {
                    clearInterval(scrollInterval);
                    scrollInterval = null;
                }
                scrollHint.style.display = 'none';
            }

            // Initialize drag events for column headers
            function initDragEvents() {
                document.querySelectorAll('.column-header[draggable="true"]').forEach(header => {
                    // Desktop events
                    header.addEventListener('dragstart', function (e) {
                        draggedColumn = this.parentElement;
                        draggedColumn.classList.add('dragging');

                        // Set drag image to an empty image for smoother visual
                        const dragImage = new Image();
                        e.dataTransfer.setDragImage(dragImage, 0, 0);
                        e.dataTransfer.effectAllowed = 'move';
                    });

                    header.addEventListener('drag', function (e) {
                        // Update indicator position during drag
                        if (draggedColumn) {
                            const rect = draggedColumn.getBoundingClientRect();
                            const scrollX = window.pageXOffset || document.documentElement.scrollLeft;
                            dragIndicator.style.left = (rect.left + scrollX - 2) + 'px';
                            dragIndicator.style.top = rect.top + 'px';
                            dragIndicator.style.height = rect.height + 'px';
                            dragIndicator.style.display = 'block';
                        }
                    });

                    header.addEventListener('dragend', function () {
                        document.querySelectorAll('.column').forEach(col => {
                            col.classList.remove('drop-before', 'drop-after', 'drop-target');
                        });

                        if (draggedColumn) {
                            draggedColumn.classList.remove('dragging');
                            draggedColumn = null;
                        }
                        dragIndicator.style.display = 'none';
                        stopAutoScroll();
                    });

                    // Mobile touch events
                    header.addEventListener('touchstart', function (e) {
                        if (isTouchDragging) return;

                        const touch = e.touches[0];
                        touchStartX = touch.clientX;
                        touchStartY = touch.clientY;

                        // Set a timeout to distinguish between tap and drag
                        touchDragTimeout = setTimeout(() => {
                            isTouchDragging = true;
                            draggedColumn = this.parentElement;
                            draggedColumn.classList.add('dragging');

                            // Prevent default to avoid scrolling while dragging
                            e.preventDefault();
                        }, 300);
                    });

                    header.addEventListener('touchmove', function (e) {
                        if (!isTouchDragging || !draggedColumn) return;

                        const touch = e.touches[0];
                        const deltaX = touch.clientX - touchStartX;
                        const deltaY = touch.clientY - touchStartY;

                        // Only start dragging if movement is primarily horizontal
                        if (Math.abs(deltaX) > 10 && Math.abs(deltaX) > Math.abs(deltaY)) {
                            e.preventDefault();

                            // Update dragged column position
                            const rect = draggedColumn.getBoundingClientRect();
                            const scrollX = window.pageXOffset || document.documentElement.scrollLeft;
                            dragIndicator.style.left = (rect.left + scrollX - 2) + 'px';
                            dragIndicator.style.top = rect.top + 'px';
                            dragIndicator.style.height = rect.height + 'px';
                            dragIndicator.style.display = 'block';

                            // Auto-scroll logic
                            const scrollThreshold = 50; // pixels from edge
                            const clientX = touch.clientX;
                            const windowWidth = window.innerWidth;

                            if (clientX > windowWidth - scrollThreshold) {
                                startAutoScroll('right');
                            } else if (clientX < scrollThreshold) {
                                startAutoScroll('left');
                            } else {
                                stopAutoScroll();
                            }

                            // Find which column we're over
                            const overElement = document.elementFromPoint(touch.clientX, touch.clientY);
                            const overColumn = overElement ? overElement.closest('.column') : null;

                            if (overColumn && overColumn !== draggedColumn && overColumn.getAttribute('data-column') !== 'features') {
                                // Clear all drop classes first
                                document.querySelectorAll('.column').forEach(col => {
                                    col.classList.remove('drop-before', 'drop-after', 'drop-target');
                                });

                                // Highlight the column being dragged over
                                overColumn.classList.add('drop-target');

                                // Determine drop position
                                const rect = overColumn.getBoundingClientRect();
                                const x = touch.clientX - rect.left;
                                const width = rect.width;

                                if (x < width / 2) {
                                    overColumn.classList.add('drop-before');
                                } else {
                                    overColumn.classList.add('drop-after');
                                }
                            }
                        }
                    });

                    header.addEventListener('touchend', function (e) {
                        clearTimeout(touchDragTimeout);
                        stopAutoScroll();

                        if (!isTouchDragging) return;
                        isTouchDragging = false;

                        // Find which column we're over
                        const touch = e.changedTouches[0];
                        const overElement = document.elementFromPoint(touch.clientX, touch.clientY);
                        const overColumn = overElement ? overElement.closest('.column') : null;

                        if (overColumn && overColumn !== draggedColumn && overColumn.getAttribute('data-column') !== 'features') {
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
                                comparisonBody.insertBefore(draggedColumn, overColumn.nextSibling);
                            }
                        }

                        // Clear drop classes
                        document.querySelectorAll('.column').forEach(col => {
                            col.classList.remove('drop-before', 'drop-after', 'drop-target');
                        });

                        if (draggedColumn) {
                            draggedColumn.classList.remove('dragging');
                            draggedColumn = null;
                        }
                        dragIndicator.style.display = 'none';
                    });

                    // Prevent context menu on long press on mobile
                    header.addEventListener('contextmenu', function (e) {
                        e.preventDefault();
                    });
                });

                // Add event listeners to columns for drop targeting (desktop only)
                document.querySelectorAll('.column').forEach(column => {
                    // Skip the features column
                    if (column.getAttribute('data-column') === 'features') return;

                    column.addEventListener('dragover', function (e) {
                        e.preventDefault();

                        if (!draggedColumn || column === draggedColumn) {
                            return;
                        }

                        const rect = column.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const width = rect.width;

                        // Clear all drop classes first
                        document.querySelectorAll('.column').forEach(col => {
                            col.classList.remove('drop-before', 'drop-after', 'drop-target');
                        });

                        // Highlight the column being dragged over
                        column.classList.add('drop-target');

                        // Determine drop position (before or after the column)
                        if (x < width / 2) {
                            // Drop before this column
                            column.classList.add('drop-before');
                        } else {
                            // Drop after this column
                            column.classList.add('drop-after');
                        }

                        // Auto-scroll logic for desktop
                        const scrollThreshold = 50; // pixels from edge
                        const clientX = e.clientX;
                        const windowWidth = window.innerWidth;

                        if (clientX > windowWidth - scrollThreshold) {
                            startAutoScroll('right');
                        } else if (clientX < scrollThreshold) {
                            startAutoScroll('left');
                        } else {
                            stopAutoScroll();
                        }
                    });

                    column.addEventListener('dragleave', function () {
                        this.classList.remove('drop-before', 'drop-after', 'drop-target');
                        stopAutoScroll();
                    });

                    column.addEventListener('drop', function (e) {
                        e.preventDefault();
                        stopAutoScroll();

                        if (!draggedColumn || draggedColumn === this) return;

                        const allColumns = Array.from(comparisonBody.querySelectorAll('.column:not([data-column="features"])'));
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
                        document.querySelectorAll('.column').forEach(col => {
                            col.classList.remove('drop-before', 'drop-after', 'drop-target');
                        });
                    });
                });
            }

            // Add new column
            addColumnBtn.addEventListener('click', function () {
                const newColumnNumber = comparisonBody.querySelectorAll('.column:not([data-column="features"])').length + 1;
                const newColumn = document.createElement('div');
                newColumn.className = 'column';
                newColumn.setAttribute('data-column', newColumnNumber);

                const randomPrice = Math.floor(Math.random() * 300) + 100;
                const randomRating = (Math.random() * 1.5 + 3.5).toFixed(1);
                const randomWarranty = Math.floor(Math.random() * 5) + 1;
                const randomWeight = (Math.random() * 1.5 + 0.5).toFixed(1);
                const randomDim1 = Math.floor(Math.random() * 10) + 5;
                const randomDim2 = Math.floor(Math.random() * 10) + 10;
                const randomDim3 = Math.floor(Math.random() * 5) + 3;

                newColumn.innerHTML = `
                    <div class="column-header" draggable="true">Product ${String.fromCharCode(64 + newColumnNumber)}</div>
                    <div class="column-content">
                        <div class="feature">$${randomPrice}</div>
                        <div class="feature">${randomRating}/5</div>
                        <div class="feature">${randomWarranty} year${randomWarranty > 1 ? 's' : ''}</div>
                        <div class="feature">${randomWeight} kg</div>
                        <div class="feature">${randomDim1}x${randomDim2}x${randomDim3} cm</div>
                    </div>
                `;

                comparisonBody.appendChild(newColumn);
                initDragEvents();
            });

            // Reset columns to original order
            resetColumnsBtn.addEventListener('click', function () {
                const featuresCol = comparisonBody.querySelector('[data-column="features"]');
                const currentColumns = Array.from(comparisonBody.querySelectorAll('.column:not([data-column="features"])'));

                // Clear current columns
                currentColumns.forEach(col => col.remove());

                // Recreate columns in original order
                originalOrder.forEach(colNum => {
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
    </script>
</body>

</html>