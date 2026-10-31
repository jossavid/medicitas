(() => {
  const menuBtn = document.getElementById('menuBtn');
  const topNav = document.getElementById('topNav');
  if (menuBtn && topNav) {
    menuBtn.addEventListener('click', () => {
      const isOpen = topNav.classList.toggle('open');
      menuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  document.querySelectorAll('form[data-confirm]').forEach(form => form.addEventListener('submit', e => {
    if (!confirm(form.dataset.confirm || '¿Continuar?')) e.preventDefault();
  }));

  document.querySelectorAll('[data-table-search]').forEach(input => {
    const table = document.querySelector(input.dataset.tableSearch);
    if (!table) return;
    input.addEventListener('input', () => filterTable(table, input.value));
  });

  const statusFilter = document.getElementById('statusFilter');
  if (statusFilter) {
    const table = document.getElementById('appointmentsTable');
    statusFilter.addEventListener('change', () => filterTable(table, statusFilter.value));
  }

  function filterTable(table, term) {
    const q = String(term || '').toLowerCase().trim();
    table.querySelectorAll('tbody tr').forEach(tr => tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none');
  }

  const roleSelect = document.getElementById('roleSelect');
  const doctorField = document.getElementById('doctorField');
  const syncDoctorField = () => { if (doctorField && roleSelect) doctorField.style.display = roleSelect.value === 'doctor' ? 'grid' : 'none'; };
  if (roleSelect) { roleSelect.addEventListener('change', syncDoctorField); syncDoctorField(); }

  const calendar = document.getElementById('calendarApp');
  if (calendar) {
    let events = [];
    try { events = JSON.parse(calendar.dataset.events || '[]'); } catch (_) {}
    let cursor = new Date(); cursor.setDate(1);
    const title = document.getElementById('calendarTitle');
    const grid = document.getElementById('calendarGrid');
    const render = () => {
      const y = cursor.getFullYear(), m = cursor.getMonth();
      title.textContent = cursor.toLocaleDateString('es-GT', {month:'long',year:'numeric'});
      grid.innerHTML = '';
      let firstDow = new Date(y,m,1).getDay(); firstDow = firstDow === 0 ? 7 : firstDow;
      const daysCurrent = new Date(y,m+1,0).getDate();
      const daysPrev = new Date(y,m,0).getDate();
      for (let i=1;i<=42;i++) {
        let day, monthOffset=0, muted=false;
        if (i < firstDow) { day = daysPrev - (firstDow-i) + 1; monthOffset=-1; muted=true; }
        else if (i >= firstDow + daysCurrent) { day = i - (firstDow + daysCurrent) + 1; monthOffset=1; muted=true; }
        else day = i - firstDow + 1;
        const d = new Date(y,m+monthOffset,day);
        const iso = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        const cell = document.createElement('div'); cell.className='calendar-day'+(muted?' is-muted':'');
        cell.innerHTML=`<div class="calendar-day-number">${day}</div>`;
        events.filter(e=>e.date===iso).slice(0,4).forEach(ev=>{
          const a=document.createElement('a');a.className=`calendar-event ${ev.status}`;a.href=`appointments/${ev.id}/edit`;a.title=`${ev.time} · ${ev.patient} · ${ev.doctor}`;a.innerHTML=`<strong>${ev.time}</strong> <span>${escapeHtml(ev.patient)}</span><small>${escapeHtml(ev.doctor)}</small>`;cell.appendChild(a);
        });
        grid.appendChild(cell);
      }
    };
    document.getElementById('prevMonth').addEventListener('click',()=>{cursor.setMonth(cursor.getMonth()-1);render();});
    document.getElementById('nextMonth').addEventListener('click',()=>{cursor.setMonth(cursor.getMonth()+1);render();});
    render();
  }

  function escapeHtml(s){return String(s).replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));}
})();
