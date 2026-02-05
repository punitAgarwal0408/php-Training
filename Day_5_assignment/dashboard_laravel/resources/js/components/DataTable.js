// DataTable.js
// Simple DataTable with sorting, filtering, and pagination
import $ from 'jquery';
import 'bootstrap';

export default class DataTable {
    constructor({ columns, data, target, pageSize = 5 }) {
        this.columns = columns;
        this.data = data;
        this.target = target;
        this.pageSize = pageSize;
        this.currentPage = 1;
        this.filter = '';
        this.sortColumn = null;
        this.sortAsc = true;
    }

    render() {
        this._renderTable();
        this._attachEvents();
    }

    _renderTable() {
        const filtered = this._getFilteredData();
        const sorted = this._getSortedData(filtered);
        const paginated = this._getPaginatedData(sorted);
        const totalPages = Math.ceil(filtered.length / this.pageSize) || 1;
        let table = `<input type='text' class='form-control mb-2' placeholder='Filter...' id='datatable-filter'>`;
        table += `<table class='table table-bordered table-hover'><thead><tr>`;
        this.columns.forEach((col, i) => {
            table += `<th data-col='${i}' class='sortable'>${col}</th>`;
        });
        table += `</tr></thead><tbody>`;
        paginated.forEach(row => {
            table += '<tr>';
            row.forEach(cell => {
                table += `<td>${cell}</td>`;
            });
            table += '</tr>';
        });
        table += `</tbody></table>`;
        table += `<nav><ul class='pagination'>`;
        for (let i = 1; i <= totalPages; i++) {
            table += `<li class='page-item${i === this.currentPage ? ' active' : ''}'><a class='page-link' href='#' data-page='${i}'>${i}</a></li>`;
        }
        table += `</ul></nav>`;
        $(this.target).html(table);
        $('#datatable-filter').val(this.filter);
    }

    _attachEvents() {
        const self = this;
        $(this.target).off();
        $(this.target).on('keyup', '#datatable-filter', function() {
            self.filter = $(this).val();
            self.currentPage = 1;
            self.render();
        });
        $(this.target).on('click', '.sortable', function() {
            const col = $(this).data('col');
            if (self.sortColumn === col) self.sortAsc = !self.sortAsc;
            else { self.sortColumn = col; self.sortAsc = true; }
            self.render();
        });
        $(this.target).on('click', '.page-link', function(e) {
            e.preventDefault();
            self.currentPage = parseInt($(this).data('page'));
            self.render();
        });
    }

    _getFilteredData() {
        if (!this.filter) return this.data;
        return this.data.filter(row => row.some(cell => String(cell).toLowerCase().includes(this.filter.toLowerCase())));
    }

    _getSortedData(data) {
        if (this.sortColumn === null) return data;
        return [...data].sort((a, b) => {
            if (a[this.sortColumn] < b[this.sortColumn]) return this.sortAsc ? -1 : 1;
            if (a[this.sortColumn] > b[this.sortColumn]) return this.sortAsc ? 1 : -1;
            return 0;
        });
    }

    _getPaginatedData(data) {
        const start = (this.currentPage - 1) * this.pageSize;
        return data.slice(start, start + this.pageSize);
    }
}
