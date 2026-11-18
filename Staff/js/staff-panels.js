document.querySelectorAll("[data-search-for]").forEach((searchInput) => {
    const target = document.getElementById(searchInput.dataset.searchFor);
    if (!target) return;

    const items = Array.from(target.querySelectorAll("[data-search-item], [data-search-card]"));
    const emptyState = target.querySelector("[data-search-empty]");
    const resultCount = document.querySelector("[data-results-count]");

    const filterItems = () => {
        const query = searchInput.value.trim().toLocaleLowerCase();
        let visibleCount = 0;

        items.forEach((item) => {
            const searchText = `${item.dataset.search || ""} ${item.textContent}`.toLocaleLowerCase();
            const isVisible = !query || searchText.includes(query);
            item.hidden = !isVisible;
            if (isVisible) visibleCount += 1;
        });

        if (emptyState) {
            emptyState.hidden = visibleCount > 0;
        }

        if (resultCount) {
            resultCount.textContent = String(visibleCount);
        }
    };

    searchInput.addEventListener("input", filterItems);
});
