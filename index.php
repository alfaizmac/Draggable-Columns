<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Draggable Comparison Tool with Auto-Scroll</title>
    <link rel="stylesheet" href="index.css">
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

    <script src="index.js"></script>
</body>

</html>