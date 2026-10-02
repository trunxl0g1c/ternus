// core/state.js
export const state = {
  data: null,
  user: null,
  csrfToken: '',
  currentPage: 'dashboard',
  searchQuery: '',
  locationFilter: '',
  showArchived: false,
  selected: new Set(),
  busy: false,
  exportHeads: [],
  exportRows: [],
};
