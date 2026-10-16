// Injetado em toda página gravada: dados fictícios, cursor visível, legendas e cartões.
(function () {
  if (window.top !== window) {
    return;
  }

  const NOMES = ['Ana', 'Bruno', 'Carla', 'Daniel', 'Eduarda', 'Felipe', 'Gabriela', 'Henrique', 'Isabela', 'João',
    'Larissa', 'Marcos', 'Natália', 'Otávio', 'Patrícia', 'Rafael', 'Sabrina', 'Thiago', 'Vanessa', 'William',
    'Beatriz', 'Caio', 'Débora', 'Fábio', 'Helena', 'Igor', 'Juliana', 'Leonardo', 'Mariana', 'Renato'];
  const SOBRENOMES = ['Almeida', 'Barbosa', 'Cardoso', 'Duarte', 'Esteves', 'Ferreira', 'Gomes', 'Holanda', 'Lima',
    'Machado', 'Nogueira', 'Oliveira', 'Pereira', 'Queiroz', 'Rocha', 'Teixeira', 'Vasconcelos', 'Moura', 'Campos',
    'Ribeiro', 'Siqueira', 'Antunes', 'Brandão', 'Coelho', 'Freitas', 'Monteiro'];
  const UFS = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI',
    'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
  const AVATAR = 'data:image/svg+xml;utf8,' + encodeURIComponent(
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><rect width="40" height="40" fill="#4154f1"/>' +
    '<text x="20" y="26" font-family="Arial" font-size="16" fill="#fff" text-anchor="middle">UD</text></svg>');

  // Elementos com dado pessoal ficam invisíveis até serem trocados (data-demo marca os já tratados).
  const SENSIVEIS = ['.users-user-name', '.users-user-email', '.dmk-docs span', '.users-user .dmk-avatar-letter',
    'img.dmk-avatar-foto', '.user-name', '.user-menu-name', '.user-menu-email', 'img.user-avatar', 'img.user-menu-avatar'];

  const termoBusca = (new URLSearchParams(location.search).get('nome') || '').trim();
  const pessoas = new WeakMap();

  function hash(texto) {
    let h = 2166136261;
    for (let i = 0; i < texto.length; i++) {
      h ^= texto.charCodeAt(i);
      h = Math.imul(h, 16777619) >>> 0;
    }
    return h;
  }

  function semente(n) {
    let x = n % 2147483648;
    return function () {
      x = (x * 1103515245 + 12345) % 2147483648;
      return x / 2147483648;
    };
  }

  function capitalizar(texto) {
    return texto.toLowerCase().replace(/(^|\s)\S/g, function (l) { return l.toUpperCase(); });
  }

  function pessoaPorTexto(original) {
    const h = hash(original.trim().toLowerCase());
    const ultimo = termoBusca ? capitalizar(termoBusca) : SOBRENOMES[(h >>> 16) % SOBRENOMES.length];
    const nome = NOMES[h % NOMES.length] + ' ' + SOBRENOMES[(h >>> 8) % SOBRENOMES.length] + ' ' + ultimo;
    const partes = nome.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().split(' ');
    return { nome: nome, email: partes[0] + '.' + partes[2] + '@exemplo.com.br', aleatorio: semente(h) };
  }

  function pessoaDaLinha(el) {
    const linha = el.closest('tr') || el.closest('.users-user');
    if (!linha) return null;
    if (pessoas.has(linha)) return pessoas.get(linha);
    const nome = linha.querySelector('.users-user-name');
    if (!nome || !nome.textContent.trim()) return null;
    const pessoa = pessoaPorTexto(nome.textContent);
    pessoas.set(linha, pessoa);
    return pessoa;
  }

  function trocarDigitos(texto, aleatorio) {
    return texto.replace(/\d/g, function () { return String(Math.floor(aleatorio() * 10)); });
  }

  function marcar(el, texto) {
    if (texto !== undefined) el.textContent = texto;
    el.setAttribute('data-demo', '1');
  }

  function tratar(el) {
    if (el.matches('.user-name, .user-menu-name')) return marcar(el, 'Usuário Demonstração');
    if (el.matches('.user-menu-email')) return marcar(el, 'demo@exemplo.com.br');
    if (el.matches('img.user-avatar, img.user-menu-avatar')) {
      el.src = AVATAR;
      return marcar(el);
    }

    const texto = el.textContent.trim();
    const pessoa = pessoaDaLinha(el);
    if (!pessoa) return;

    if (el.matches('img.dmk-avatar-foto')) {
      const letra = document.createElement('span');
      letra.className = 'dmk-avatar-letter sm';
      marcar(letra, pessoa.nome.charAt(0));
      el.replaceWith(letra);
    } else if (el.matches('.dmk-avatar-letter')) {
      marcar(el, pessoa.nome.charAt(0));
    } else if (el.matches('.users-user-name')) {
      if (texto) marcar(el, pessoa.nome);
    } else if (el.matches('.dmk-docs span')) {
      if (!texto) return;
      if (/^OAB/i.test(texto)) {
        const uf = (texto.toUpperCase().match(/[A-Z]{2}/g) || []).filter(function (s) { return UFS.indexOf(s) !== -1; })[0] || 'SP';
        marcar(el, 'OAB/' + uf + ' ' + trocarDigitos('123.456', pessoa.aleatorio));
      } else {
        marcar(el, trocarDigitos(texto, pessoa.aleatorio));
      }
      if (el.title) el.title = el.textContent;
    } else if (el.matches('.users-user-email')) {
      if (texto) marcar(el, texto.indexOf('@') !== -1 ? pessoa.email : texto);
    }
  }

  const PENDENTES = SENSIVEIS.map(function (s) { return s + ':not([data-demo])'; }).join(',');

  function varrer() {
    if (!document.documentElement) return;
    // O nome define a pessoa da linha; trata-se primeiro para avatar e documentos usarem o mesmo fictício.
    document.querySelectorAll('.users-user-name:not([data-demo])').forEach(tratar);
    document.querySelectorAll(PENDENTES).forEach(tratar);
  }

  const estilo = document.createElement('style');
  estilo.textContent =
    PENDENTES + '{visibility:hidden!important}' +
    '#demo-cursor{position:fixed;left:0;top:0;width:22px;height:22px;z-index:2147483647;pointer-events:none;' +
    'transition:opacity .3s;filter:drop-shadow(0 1px 2px rgba(0,0,0,.45))}' +
    'html.demo-com-cartao #demo-cursor{opacity:0}' +
    '#demo-cursor.clique::after{content:"";position:absolute;left:-14px;top:-14px;width:28px;height:28px;border-radius:50%;' +
    'background:rgba(65,84,241,.35);animation:demo-clique .45s ease-out forwards}' +
    '@keyframes demo-clique{from{transform:scale(.3);opacity:1}to{transform:scale(1.6);opacity:0}}' +
    '#demo-legenda{position:fixed;left:50%;bottom:30px;transform:translateX(-50%);max-width:900px;z-index:2147483646;' +
    'background:rgba(15,23,42,.88);color:#fff;font:500 20px/1.4 Poppins,Arial,sans-serif;padding:12px 24px;border-radius:12px;' +
    'box-shadow:0 8px 30px rgba(0,0,0,.25);text-align:center;opacity:0;transition:opacity .35s;pointer-events:none}' +
    '#demo-legenda.visivel{opacity:1}' +
    '#demo-cartao{position:fixed;inset:0;z-index:2147483645;display:flex;flex-direction:column;align-items:center;justify-content:center;' +
    'background:linear-gradient(135deg,#1e2a78,#4154f1);color:#fff;font-family:Poppins,Arial,sans-serif;opacity:0;transition:opacity .5s;pointer-events:none}' +
    '#demo-cartao.visivel{opacity:1}#demo-cartao h1{font-size:54px;font-weight:700;margin:0 0 12px;color:#fff}' +
    '#demo-cartao p{font-size:24px;opacity:.85;margin:0}';

  new MutationObserver(function () {
    if (document.documentElement && !estilo.isConnected) {
      document.documentElement.appendChild(estilo);
    }
    varrer();
  }).observe(document, { childList: true, subtree: true, characterData: true });

  function preparar() {
    varrer();

    const cursor = document.createElement('div');
    cursor.id = 'demo-cursor';
    cursor.innerHTML = '<svg viewBox="0 0 24 24" width="22" height="22"><path d="M3 2l7 19 2.6-7.4L20 11z" fill="#111" stroke="#fff" stroke-width="1.5" stroke-linejoin="round"/></svg>';
    let posicao = { x: -40, y: -40 };
    try { posicao = JSON.parse(sessionStorage.getItem('demoPosicao')) || posicao; } catch (err) {}
    cursor.style.transform = 'translate(' + posicao.x + 'px,' + posicao.y + 'px)';
    document.body.appendChild(cursor);
    document.addEventListener('mousemove', function (e) {
      cursor.style.transform = 'translate(' + e.clientX + 'px,' + e.clientY + 'px)';
      try { sessionStorage.setItem('demoPosicao', JSON.stringify({ x: e.clientX, y: e.clientY })); } catch (err) {}
    }, true);
    document.addEventListener('mousedown', function () {
      cursor.classList.remove('clique');
      void cursor.offsetWidth;
      cursor.classList.add('clique');
    }, true);

    const legenda = document.createElement('div');
    legenda.id = 'demo-legenda';
    document.body.appendChild(legenda);

    const cartao = document.createElement('div');
    cartao.id = 'demo-cartao';
    cartao.innerHTML = '<h1></h1><p></p>';
    document.body.appendChild(cartao);
  }

  window.demoLegenda = function (texto) {
    const el = document.getElementById('demo-legenda');
    if (!el) return;
    if (!texto) { el.classList.remove('visivel'); return; }
    el.textContent = texto;
    el.classList.add('visivel');
  };

  window.demoCartao = function (titulo, subtitulo) {
    const el = document.getElementById('demo-cartao');
    if (!el) return;
    document.documentElement.classList.toggle('demo-com-cartao', !!titulo);
    if (!titulo) { el.classList.remove('visivel'); return; }
    el.querySelector('h1').textContent = titulo;
    el.querySelector('p').textContent = subtitulo || '';
    el.classList.add('visivel');
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', preparar);
  } else {
    preparar();
  }
})();
