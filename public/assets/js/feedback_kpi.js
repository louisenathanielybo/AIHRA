// This script updates the Most Common Flagged Reason KPI in the feedback section
function updateFlaggedReasonKPISection() {
    const flaggedRows = document.querySelectorAll('#flaggedSectionTable tbody tr');
    const reasonCounts = {};
    let visibleCount = 0;
    flaggedRows.forEach(row => {
        if (row.style.display === 'none' || row.querySelector('td[colspan]')) return;
        const reasonCell = row.cells[2];
        let reason = reasonCell ? reasonCell.textContent.trim() : 'Unknown';
        if (!reason) reason = 'Unknown';
        reasonCounts[reason] = (reasonCounts[reason] || 0) + 1;
        visibleCount++;
    });
    let mostCommon = 'N/A', mostCount = 0, tiedReasons = [];
    for (const [reason, count] of Object.entries(reasonCounts)) {
        if (count > mostCount) {
            mostCount = count;
            tiedReasons = [reason];
        } else if (count === mostCount && count > 0) {
            tiedReasons.push(reason);
        }
    }
    if (tiedReasons.length === 1) {
        mostCommon = tiedReasons[0];
    } else if (tiedReasons.length > 1) {
        mostCommon = tiedReasons.join(', ');
    }
    const valueElem = document.getElementById('commonFlaggedReasonValueSection');
    const countElem = document.getElementById('commonFlaggedReasonCountSection');
    if (valueElem) valueElem.textContent = mostCommon;
    if (countElem) countElem.textContent = `Based on ${visibleCount} flag${visibleCount === 1 ? '' : 's'}`;
}

document.addEventListener('DOMContentLoaded', function() {
    updateFlaggedReasonKPISection();
    // Hook into global date range filter if available
    if (window.applyDateRangeFilter) {
        const orig = window.applyDateRangeFilter;
        window.applyDateRangeFilter = function() {
            if (typeof orig === 'function') orig.apply(this, arguments);
            updateFlaggedReasonKPISection();
        };
    }
    // Also listen for custom events if used elsewhere
    document.addEventListener('dateRangeChanged', updateFlaggedReasonKPISection);
});
