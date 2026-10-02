import { html } from '../core/html.js';
// components/modal.js
import { btn } from './ui.js';
import { modal } from '../core/dom.js';
import { e } from '../core/format.js';
import { state } from '../core/state.js';
export function openForm(title, contentMarkup, onSubmit, button = 'Simpan') {
  modal.innerHTML = html`<form id="modal-form">
    <div class="modal-head">
      <h2>${e(title)}</h2>
      ${btn('✕', 'close', '', 'quiet')}
    </div>
    <div class="modal-body">
      ${contentMarkup}
      <div id="form-error"></div>
    </div>
    <div class="modal-foot">
      ${btn('Kembali', 'close')}<button class="btn primary" type="submit">${e(button)}</button>
    </div>
  </form>`;
  modal.showModal();
  document.querySelector('#modal-form').onsubmit = async (ev) => {
    ev.preventDefault();
    if (state.busy) return;
    state.busy = true;
    const b = ev.target.querySelector('[type=submit]');
    b.disabled = true;
    document.querySelector('#form-error').innerHTML = '';
    try {
      await onSubmit(Object.fromEntries(new FormData(ev.target)), ev.target);
      modal.close();
    } catch (err) {
      document.querySelector('#form-error').innerHTML = html`<div class="error">
        ${e(err.message)}
      </div>`;
    } finally {
      b.disabled = false;
      state.busy = false;
    }
  };
}
export function showDetail(title, contentMarkup, print = false) {
  modal.innerHTML = html`<div class="modal-head">
      <h2>${e(title)}</h2>
      ${btn('✕', 'close', '', 'quiet')}
    </div>
    <div class="modal-body">${contentMarkup}</div>
    <div class="modal-foot">
      ${btn('Tutup', 'close')}${print ? btn('Cetak / Simpan PDF', 'print', '', 'primary') : ''}
    </div>`;
  modal.showModal();
}
