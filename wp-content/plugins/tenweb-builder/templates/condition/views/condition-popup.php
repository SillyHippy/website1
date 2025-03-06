<?php
/**
 * Created by PhpStorm.
 * User: mher
 * Date: 10/3/18
 * Time: 3:07 PM
 */
//phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
echo "<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900italic,900' rel='stylesheet' type='text/css'>";
?>
<div class="twbb-condition-popup-overlay">
    <div class="twbb-condition-popup">
        <div class="twbb-condition-popup-header">
            <span><?php esc_html_e("Display Conditions", "tenweb-builder") ?></span>
            <span class="twbb-condition-popup-close"></span>
        </div>
        <div class="twbb-condition-popup-content">
            <div class="twbb-condition-popup-loader">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2aWV3Qm94PSIwIDAgNDYgNDYuMDAxIj48ZGVmcz48c3R5bGU+LmF7ZmlsbDojZTNlOGVkO30uYntmaWxsOnVybCgjYSk7fTwvc3R5bGU+PGxpbmVhckdyYWRpZW50IGlkPSJhIiB4MT0iMC4xNjEiIHkxPSIxIiB4Mj0iMC45MDciIHkyPSIwLjkyNiIgZ3JhZGllbnRVbml0cz0ib2JqZWN0Qm91bmRpbmdCb3giPjxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iI0E0QUZCNyIgc3RvcC1vcGFjaXR5PSIwIi8+PHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjQTRBRkI3Ii8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTkzNy4wMDEgLTQ4OCkiPjxjaXJjbGUgY2xhc3M9ImEiIGN4PSIyMCIgY3k9IjIwIiByPSIyMCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoOTQwIDQ5MSkiLz48cGF0aCBjbGFzcz0iYiIgZD0iTS0zNzU2LjgyNiw0Nzg4aDB2LTNhMTkuODY2LDE5Ljg2NiwwLDAsMCwxNC4xMzYtNS44NjUsMTkuODYzLDE5Ljg2MywwLDAsMCw1Ljg2NS0xNC4xMzUsMjAuMDIzLDIwLjAyMywwLDAsMC0yMC0yMCwyMC4wMjMsMjAuMDIzLDAsMCwwLTIwLDIwLDE5Ljg0LDE5Ljg0LDAsMCwwLDYuMDU5LDE0LjMyN2wtMS42NTksMi41NzRjLS4yMzktLjIyMS0uNDYxLS40MzYtLjY2My0uNjM3YTIyLjg1LDIyLjg1LDAsMCwxLTYuNzM2LTE2LjI2NCwyMi44NTIsMjIuODUyLDAsMCwxLDYuNzM2LTE2LjI2NCwyMi44NDksMjIuODQ5LDAsMCwxLDE2LjI2NC02LjczNiwyMi44NDksMjIuODQ5LDAsMCwxLDE2LjI2NCw2LjczNiwyMi44NTIsMjIuODUyLDAsMCwxLDYuNzM2LDE2LjI2NCwyMi44NSwyMi44NSwwLDAsMS02LjczNiwxNi4yNjRBMjIuODUzLDIyLjg1MywwLDAsMS0zNzU2LjgyNiw0Nzg4WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNDcxNi44MjggLTQyNTQpIi8+PC9nPjwvc3ZnPg=="/>
            </div>
            <p class="twbb-condition-popup-text-1">
              <?php esc_html_e("Where Do You Want to Display Your Template?", "tenweb-builder") ?>
            </p>
            <p class="twbb-condition-popup-text-2">
              <?php
              esc_html_e("Set the conditions that determine where your Template is used through your site.", "tenweb-builder");
              ?>
            </p>
            <p class="twbb-condition-popup-text-2">
              <?php
              esc_html_e("For example, choose 'Entire Site' to display the template across your site.", "tenweb-builder");
              ?>
            </p>
            <div class="twbb-condition-section-wrapper"></div>
            <div class="twbb-condition-notif-container" style="display: none;"></div>
            <div class="twbb-condition-add-new-btn">
                <a id="twbb-condition-add-new"><?php esc_html_e('Add Condition', 'tenweb-builder'); ?></a>
            </div>
        </div>
        <div class="twbb-condition-footer">
            <a id="twbb-condition-save"><?php esc_html_e('Publish', 'tenweb-builder') ?></a>
        </div>
    </div>
</div>
