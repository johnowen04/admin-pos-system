// Store if we've already initialized event listeners
let listenersInitialized = false;
let debounceLoading = false;

// Add CSS for performance optimization
document.addEventListener("DOMContentLoaded", function () {
    // Add performance-optimizing CSS and loading indicator styles
    const style = document.createElement("style");
    style.textContent = `
        details:not([open]) .table-responsive {
            display: none;
        }
        
        .permission-checkbox, .feature-all, .role-all, .operation-all {
            will-change: transform;
        }
        
        #permissions-container {
            transform: translateZ(0);
            will-change: transform;
        }
        
        /* Loading indicator styles */
        .acl-loading-indicator {
            position: fixed;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            z-index: 1050;
            display: flex;
            align-items: center;
            transition: opacity 0.3s;
            opacity: 0;
            pointer-events: none;
        }
        
        .acl-loading-indicator.active {
            opacity: 1;
        }
        
        .acl-loading-indicator .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            margin-right: 8px;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);

    // Create loading indicator
    const loadingIndicator = document.createElement("div");
    loadingIndicator.className = "acl-loading-indicator";
    loadingIndicator.innerHTML =
        '<div class="spinner"></div><div>Processing changes...</div>';
    document.body.appendChild(loadingIndicator);

    // Initialize details elements
    const details = document.querySelectorAll("details");
    details.forEach((detail) => {
        // Don't open all details by default
        detail.open = false;

        // Update checkbox states when a details element is opened
        detail.addEventListener("toggle", function () {
            if (
                this.open &&
                document.getElementById("aclForm").classList.contains("editing")
            ) {
                // Show loading indicator
                showLoading();
                // Update the header checkbox states for this feature
                updateAllCheckboxStates();
            }
        });
    });
});

// Function to show loading indicator
function showLoading() {
    debounceLoading = true;
    const loadingIndicator = document.querySelector(".acl-loading-indicator");
    if (loadingIndicator) {
        loadingIndicator.classList.add("active");
    }
}

// Function to hide loading indicator
function hideLoading() {
    debounceLoading = false;
    const loadingIndicator = document.querySelector(".acl-loading-indicator");
    if (loadingIndicator) {
        loadingIndicator.classList.remove("active");
    }
}

// Function to enable all checkboxes before form submission
function prepareFormSubmission() {
    // Show loading indicator during submission
    showLoading();

    // Enable ALL permission checkboxes before submitting, including those in closed details
    const allCheckboxes = document.querySelectorAll(".permission-checkbox");
    allCheckboxes.forEach((checkbox) => {
        checkbox.disabled = false;
    });

    // Return true to allow the form submission to proceed
    return true;
}

function toggleEditMode() {
    const aclForm = document.getElementById("aclForm");
    const toggleEditBtn = document.getElementById("toggleEditBtn");
    const cancelEditBtn = document.getElementById("cancelEditBtn");
    const saveChangesBtn = document.getElementById("saveChangesBtn");

    // Use more efficient selectors and reduce DOM operations
    const openDetailsElements = document.querySelectorAll("details[open]");

    // Show loading indicator
    showLoading();

    // Switch to edit mode
    aclForm.classList.add("editing");

    // Hide edit button, show cancel and save buttons
    toggleEditBtn.style.display = "none";
    cancelEditBtn.style.display = "inline-block";
    saveChangesBtn.style.display = "inline-block";

    // Only enable checkboxes in open details elements for better performance
    openDetailsElements.forEach((detail) => {
        const checkboxes = detail.querySelectorAll(
            ".permission-checkbox, .operation-all"
        );
        checkboxes.forEach((cb) => (cb.disabled = false));
    });

    // Initialize all header checkbox states and add event listeners (only once)
    if (!listenersInitialized) {
        initializeCheckboxes();
        listenersInitialized = true;
    }

    // Update states in case anything changed, but only for open details
    updateAllCheckboxStates();
}

function cancelEdit() {
    const aclForm = document.getElementById("aclForm");
    const toggleEditBtn = document.getElementById("toggleEditBtn");
    const cancelEditBtn = document.getElementById("cancelEditBtn");
    const saveChangesBtn = document.getElementById("saveChangesBtn");
    const checkboxes = document.querySelectorAll(
        ".permission-checkbox, .operation-all"
    );

    // Show loading indicator
    showLoading();

    // Switch to view mode
    aclForm.classList.remove("editing");

    // Show edit button, hide cancel and save buttons
    toggleEditBtn.style.display = "inline-block";
    cancelEditBtn.style.display = "none";
    saveChangesBtn.style.display = "none";

    // Disable all checkboxes
    checkboxes.forEach((cb) => (cb.disabled = true));

    // Reset form to original state
    aclForm.reset();

    // Hide loading after reset
    hideLoading();
}

function initializeCheckboxes() {
    // Get all checkboxes with improved selector performance
    const container = document.getElementById("permissions-container");

    // Operation-all checkboxes - check all permissions across all roles for this feature-operation
    container.querySelectorAll(".operation-all").forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            // Show loading indicator
            showLoading();

            const feature = this.dataset.feature;
            const operation = this.dataset.operation;
            const currentDetails = this.closest("details");

            if (currentDetails && currentDetails.open) {
                const isChecked = this.checked;
                
                // Special handling for * operation
                if (operation === "*") {
                    // Only select the permission-checkbox with operation=* for all roles
                    const wildcardCheckboxes = currentDetails.querySelectorAll(
                        `.permission-checkbox[data-feature="${feature}"][data-operation="*"]:not([disabled])`
                    );
                    
                    requestAnimationFrame(() => {
                        wildcardCheckboxes.forEach((cb) => {
                            cb.checked = isChecked;
                            
                            // Always trigger their change event to cascade effect - both for checking AND unchecking
                            const event = new Event('change', { bubbles: true });
                            cb.dispatchEvent(event);
                        });
                        updateAllCheckboxStates();
                    });
                } else {
                    // Regular behavior for non-* operations - check all roles for this operation
                    const checkboxes = currentDetails.querySelectorAll(
                        `.permission-checkbox[data-feature="${feature}"][data-operation="${operation}"]:not([disabled])`
                    );
                    
                    requestAnimationFrame(() => {
                        checkboxes.forEach((cb) => (cb.checked = isChecked));
                        updateAllCheckboxStates();
                    });
                }
            }
        });
    });

    // Use event delegation for permission checkboxes to improve performance
    container.addEventListener("change", function (e) {
        const target = e.target;
        if (!target.classList.contains("permission-checkbox")) return;

        // Show loading indicator
        showLoading();

        // Special handling for wildcard operation (*)
        if (target.dataset.operation === "*") {
            const feature = target.dataset.feature;
            const role = target.dataset.role;
            const isChecked = target.checked;
            const currentDetails = target.closest("details");

            if (currentDetails && currentDetails.open) {
                // Find all checkboxes for the same role and feature
                const relatedCheckboxes = currentDetails.querySelectorAll(
                    `.permission-checkbox[data-feature="${feature}"][data-role="${role}"]:not([data-operation="*"]):not([disabled])`
                );

                // Update all related checkboxes
                requestAnimationFrame(() => {
                    relatedCheckboxes.forEach((cb) => (cb.checked = isChecked));
                    updateAllCheckboxStates();
                });
            }
        }

        // Avoid excessive DOM operations by batching updates
        requestAnimationFrame(() => {
            // Update header checkbox states
            updateAllCheckboxStates();
        });
    });

    // Initialize operation checkboxes state for open details
    updateAllCheckboxStates();
}

// Optimized update function with debouncing
const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
            // Hide loading indicator when updates are done
            hideLoading();
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Also update the updateAllCheckboxStates function to handle special case for * operation
const updateAllCheckboxStates = debounce(() => {
    // Only update checkboxes in open details elements
    const openDetailsElements = document.querySelectorAll("details[open]");

    // Batch DOM reads and writes for better performance
    const operationUpdates = [];
    const wildCardUpdates = [];

    // Process each open details element separately
    openDetailsElements.forEach((detail) => {
        // Operation checkboxes - only consider current detail
        detail
            .querySelectorAll(".operation-all:not([disabled])")
            .forEach((checkbox) => {
                const feature = checkbox.dataset.feature;
                const operation = checkbox.dataset.operation;

                // Only consider checkboxes within this details element
                const checkboxes = detail.querySelectorAll(
                    `.permission-checkbox[data-feature="${feature}"][data-operation="${operation}"]:not([disabled])`
                );

                if (checkboxes.length > 0) {
                    const allChecked = Array.from(checkboxes).every(
                        (cb) => cb.checked
                    );
                    operationUpdates.push({ checkbox, checked: allChecked });
                }
            });

        // Check wildcard (*) operation checkboxes status based on if all operations are checked
        detail
            .querySelectorAll(
                '.permission-checkbox[data-operation="*"]:not([disabled])'
            )
            .forEach((wildcardCheckbox) => {
                const feature = wildcardCheckbox.dataset.feature;
                const role = wildcardCheckbox.dataset.role;

                // Get all non-wildcard checkboxes for this feature and role
                const normalCheckboxes = detail.querySelectorAll(
                    `.permission-checkbox[data-feature="${feature}"][data-role="${role}"]:not([data-operation="*"]):not([disabled])`
                );

                if (normalCheckboxes.length > 0) {
                    // A wildcard should be checked only if ALL operations are checked
                    const allOperationsChecked = Array.from(
                        normalCheckboxes
                    ).every((cb) => cb.checked);
                    wildCardUpdates.push({
                        checkbox: wildcardCheckbox,
                        checked: allOperationsChecked,
                    });
                }
            });
    });

    // Apply all updates at once (DOM writes)
    requestAnimationFrame(() => {
        operationUpdates.forEach(
            (update) => (update.checkbox.checked = update.checked)
        );
        wildCardUpdates.forEach(
            (update) => (update.checkbox.checked = update.checked)
        );
    });
}, 50);

// Enable checkboxes in a details element when it's opened while in edit mode
document.addEventListener("click", function (e) {
    // If we're in edit mode and clicking on a summary element
    if (
        document.getElementById("aclForm") &&
        document.getElementById("aclForm").classList.contains("editing")
    ) {
        const summary = e.target.closest("summary");
        if (summary) {
            const details = summary.parentElement;

            // If we're opening the details, enable all checkboxes within it
            if (!details.hasAttribute("open")) {
                // Show loading indicator
                showLoading();

                setTimeout(() => {
                    const checkboxes = details.querySelectorAll(
                        ".permission-checkbox, .operation-all"
                    );
                    checkboxes.forEach((cb) => (cb.disabled = false));

                    // Update checkbox states for this newly opened section
                    updateAllCheckboxStates();
                }, 0);
            }
        }
    }
});
