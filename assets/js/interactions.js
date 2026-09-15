(() => {
 const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 function modal(title,html){const d=document.createElement('dialog');d.innerHTML=`<header><h2>${esc(title)}</h2><button type="button" class="secondary-button">Tutup</button></header><div>${html}</div>`;document.body.append(d);d.querySelector('button').onclick=()=>d.close();d.addEventListener('close',()=>d.remove());d.showModal();return d;}
 async function api(route,data){const r=await fetch('api/v1/index.php?route='+route,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify(data)});const body=await r.json();if(!r.ok)throw Error(body.message||'Tindakan gagal');return body.data;}
 document.querySelectorAll('[data-decision]').forEach(b=>b.onclick=async()=>{const notes=prompt('Catatan keputusan'+(b.dataset.decision==='approve'?' (opsional)':' (wajib)'));if(notes===null)return;if(b.dataset.decision!=='approve'&&!notes.trim()){modal('Catatan diperlukan','Isi alasan revisi atau penolakan.');return;}if(!confirm('Simpan keputusan ini?'))return;b.disabled=true;try{await api('approvals/'+b.dataset.step+'/'+b.dataset.decision,{notes});location.reload();}catch(e){modal('Keputusan belum tersimpan',esc(e.message));b.disabled=false;}});
 document.querySelector('[data-print-document]')?.addEventListener('click',()=>window.print());
 document.querySelector('[data-submit-document]')?.addEventListener('click',async e=>{if(!confirm('Ajukan surat ini untuk pemeriksaan?'))return;const b=e.currentTarget;b.disabled=true;try{await api('documents/'+b.dataset.submitDocument+'/submit',{});location.reload();}catch(err){modal('Pengajuan belum berhasil',esc(err.message));b.disabled=false;}});
 const search=document.querySelector('[data-table-search]');if(search){const q=new URLSearchParams(location.search).get('q');if(q){search.value=q;search.dispatchEvent(new Event('input'));}}
 function exportRows(){const rows=[...document.querySelectorAll('[data-row]')].filter(r=>!r.hidden);if(!rows.length){modal('Ekspor','Tidak ada data yang sesuai filter.');return;}const csv='\uFEFF'+rows.map(r=>[...r.querySelectorAll('td')].length?[...r.querySelectorAll('td')].map(c=>c.innerText):[r.innerText]).map(c=>c.map(v=>'"'+(/^[=+@-]/.test(v)?"'":'')+v.replaceAll('"','""')+'"').join(',')).join('\r\n');const url=URL.createObjectURL(new Blob([csv],{type:'text/csv;charset=utf-8'}));const a=document.createElement('a');a.href=url;a.download='simantap-'+new Date().toISOString().slice(0,10)+'.csv';a.click();setTimeout(()=>URL.revokeObjectURL(url),1000);}
 document.querySelectorAll('button').forEach(b=>{const label=b.textContent.trim();
 if(['Ekspor Log','Ekspor Rekap'].includes(label)||(b.closest('.filter-group')&&b.querySelector('.ri-download-2-line'))){b.onclick=exportRows;b.setAttribute('aria-label','Ekspor hasil ke CSV');}
 if(label==='Filter'||label==='Cari arsip'){b.onclick=()=>{search?.focus();search?.dispatchEvent(new Event('input'));document.querySelector('[data-status-filter]')?.focus();};}
 if(label==='Pratinjau'&&b.closest('.template-card')){b.onclick=()=>{const card=b.closest('.template-card');const title=card.querySelector('h2').textContent;modal('Pratinjau '+title,`<p>Contoh tata letak ${esc(title)}. Data penerbitan dan nomor diisi saat surat dibuat.</p><div class="paper"><div class="letter-head"><strong>UNIVERSITAS MUHAMMADIYAH PONOROGO</strong></div><h3>${esc(title.toUpperCase())}</h3><u>Nomor: [nomor surat]</u><p>Perihal: [judul dokumen]</p><p>[Isi dokumen sesuai jenis surat]</p><div class="signature">[Tanggal penerbitan]<br>[Pejabat penandatangan]</div></div>`);};}
 if(label==='Tandai semua dibaca'){b.onclick=async()=>{b.disabled=true;try{await api('notifications/read-all',{});location.reload();}catch(e){modal('Notifikasi',esc(e.message));b.disabled=false;}};}
 });
 let zoom=100;document.querySelectorAll('.preview-toolbar button').forEach(b=>{b.type='button';const plus=!!b.querySelector('.ri-add-line');b.setAttribute('aria-label',plus?'Perbesar pratinjau':'Perkecil pratinjau');b.onclick=()=>{zoom=Math.max(50,Math.min(150,zoom+(plus?10:-10)));document.querySelector('.paper').style.zoom=zoom+'%';document.querySelector('.preview-toolbar b').textContent=zoom+'%';};});
  // ==========================================
  // NOTIFICATION DRAWER (OFF-CANVAS)
  // ==========================================
  window.toggleNotificationDrawer = function(open) {
    const drawer = document.getElementById('notifDrawer');
    const backdrop = document.getElementById('notifDrawerBackdrop');
    if (!drawer || !backdrop) return;
    const shouldOpen = (open === undefined) ? !drawer.classList.contains('open') : !!open;
    if (shouldOpen) {
      drawer.classList.add('open');
      backdrop.classList.add('show');
      drawer.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    } else {
      drawer.classList.remove('open');
      backdrop.classList.remove('show');
      drawer.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }
  };

  window.filterDrawerNotifs = function(filter, btn) {
    const tabs = document.querySelectorAll('.drawer-tab');
    tabs.forEach(t => t.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const items = document.querySelectorAll('.drawer-notif-item');
    let visibleCount = 0;
    items.forEach(item => {
      const isUnread = item.dataset.unread === '1';
      if (filter === 'unread') {
        if (isUnread) {
          item.style.display = 'flex';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      } else {
        item.style.display = 'flex';
        visibleCount++;
      }
    });

    let empty = document.getElementById('drawerFilteredEmpty');
    if (visibleCount === 0 && items.length > 0) {
      if (!empty) {
        empty = document.createElement('div');
        empty.id = 'drawerFilteredEmpty';
        empty.className = 'drawer-empty-state';
        empty.innerHTML = '<span class="empty-icon"><i class="ri-checkbox-circle-line"></i></span><h4>Semua Notifikasi Terbaca</h4><p>Tidak ada notifikasi yang belum dibaca.</p>';
        document.getElementById('drawerNotifList')?.append(empty);
      }
      empty.style.display = 'block';
    } else if (empty) {
      empty.style.display = 'none';
    }
  };

  window.markAllNotificationsRead = async function() {
    const btn = document.getElementById('drawerMarkAllBtn');
    if (btn) btn.disabled = true;
    try {
      await api('notifications/read-all', {});
      // Update UI in real-time
      document.querySelectorAll('.drawer-notif-item').forEach(item => {
        item.classList.remove('unread');
        item.dataset.unread = '0';
        item.querySelector('.notif-unread-dot')?.remove();
      });
      const topbarDot = document.getElementById('topbarNotifDot');
      if (topbarDot) topbarDot.style.display = 'none';

      const drawerBadge = document.getElementById('drawerUnreadBadge');
      if (drawerBadge) drawerBadge.style.display = 'none';

      const tabCount = document.getElementById('tabUnreadCount');
      if (tabCount) tabCount.textContent = '0';

      // If on unread filter, re-apply filter
      const activeTab = document.querySelector('.drawer-tab.active');
      if (activeTab && activeTab.dataset.filter === 'unread') {
        window.filterDrawerNotifs('unread', activeTab);
      }
    } catch (e) {
      modal('Notifikasi', esc(e.message));
    } finally {
      if (btn) btn.disabled = false;
    }
  };

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      window.toggleNotificationDrawer(false);
    }
  });
})();

