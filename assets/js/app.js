(() => {
 const root=document.documentElement,sidebar=document.getElementById('appSidebar'),backdrop=document.querySelector('.sidebar-backdrop');
 const saved=localStorage.getItem('simantap-theme'); if(saved) root.dataset.theme=saved;
 const syncTheme=()=>{const i=document.querySelector('#themeToggle i');if(i)i.className=root.dataset.theme==='dark'?'ri-sun-line':'ri-moon-line'};syncTheme();
 document.getElementById('themeToggle')?.addEventListener('click',()=>{root.dataset.theme=root.dataset.theme==='dark'?'light':'dark';localStorage.setItem('simantap-theme',root.dataset.theme);syncTheme()});
 const close=()=>{sidebar?.classList.remove('open');backdrop?.classList.remove('show')};
 document.getElementById('menuToggle')?.addEventListener('click',()=>{sidebar?.classList.toggle('open');backdrop?.classList.toggle('show')});backdrop?.addEventListener('click',close);
 document.addEventListener('keydown',e=>{if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='k'){e.preventDefault();document.getElementById('globalSearch')?.focus()}if(e.key==='Escape')close()});
 const search=document.querySelector('[data-table-search]'),status=document.querySelector('[data-status-filter]'),rows=[...document.querySelectorAll('[data-row]')];
 const filter=()=>{const q=(search?.value||'').toLowerCase(),s=status?.value||'';rows.forEach(r=>r.hidden=!(r.textContent.toLowerCase().includes(q)&&(!s||r.dataset.status===s)))};search?.addEventListener('input',filter);status?.addEventListener('change',filter);
 const form=document.getElementById('documentForm'),state=document.getElementById('saveState');
 const save=()=>{if(!form)return;localStorage.setItem('simantap-draft',JSON.stringify([...new FormData(form).entries()]));if(state){state.textContent='Tersimpan otomatis';setTimeout(()=>state.textContent='Semua perubahan tersimpan',900)}};
 form?.addEventListener('input',()=>{if(state)state.textContent='Menyimpan...';clearTimeout(window.draftTimer);window.draftTimer=setTimeout(save,600)});document.getElementById('saveDraft')?.addEventListener('click',save);
 form?.addEventListener('submit',()=>{save();if(state)state.textContent='Menyimpan ke database...'});
 document.getElementById('globalSearch')?.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();location.href='?page=documents&q='+encodeURIComponent(e.target.value)}});
 if('serviceWorker' in navigator) navigator.serviceWorker.register('service-worker.js').catch(()=>{});
})();
