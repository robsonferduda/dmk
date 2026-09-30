(function () {
  'use strict';

  const baseUrl = (document.querySelector('meta[name="base-url"]') || {}).content || '';

  const choicesPadrao = {
    itemSelectText: '',
    shouldSort: false,
    searchResultLimit: 50,
    searchPlaceholderValue: 'Digite para buscar',
    noResultsText: 'Nenhum resultado',
    noChoicesText: 'Nenhuma opção disponível',
    allowHTML: false
  };

  function select(el, opcoes) {
    if (!el || typeof Choices === 'undefined') {
      return null;
    }
    return new Choices(el, Object.assign({}, choicesPadrao, opcoes || {}));
  }

  /**
   * Liga um select de estado a um select de cidades carregadas por AJAX (rota clássica cidades-por-estado).
   */
  function cidadesPorEstado(estadoEl, cidadeEl, opcoes) {
    opcoes = Object.assign({ selecionada: '', rotuloVazio: 'Todas as cidades' }, opcoes || {});

    const estadoChoices = select(estadoEl);
    const cidadeChoices = select(cidadeEl);
    let selecionada = String(opcoes.selecionada || '');

    function preencher(lista) {
      const itens = [{ value: '', label: opcoes.rotuloVazio, selected: selecionada === '' }];
      lista.forEach(function (cidade) {
        const valor = String(cidade.cd_cidade_cde);
        itens.push({ value: valor, label: cidade.nm_cidade_cde, selected: valor === selecionada });
      });
      cidadeChoices.setChoices(itens, 'value', 'label', true);
    }

    function carregar(estado) {
      if (!cidadeChoices) {
        return;
      }

      cidadeChoices.clearStore();

      if (!estado) {
        preencher([]);
        cidadeChoices.disable();
        return;
      }

      cidadeChoices.enable();
      cidadeChoices.setChoices([{ value: '', label: 'Carregando cidades...', selected: true, disabled: true }], 'value', 'label', true);

      fetch(baseUrl + '/cidades-por-estado/' + encodeURIComponent(estado), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        credentials: 'same-origin'
      })
        .then(function (resposta) { return resposta.json(); })
        .then(function (lista) {
          cidadeChoices.clearStore();
          preencher(lista || []);
          selecionada = '';
        })
        .catch(function () {
          cidadeChoices.clearStore();
          cidadeChoices.setChoices([{ value: '', label: 'Erro ao carregar cidades', selected: true }], 'value', 'label', true);
        });
    }

    if (estadoEl) {
      estadoEl.addEventListener('change', function () {
        selecionada = '';
        carregar(estadoEl.value);
      });
      carregar(estadoEl.value);
    }

    return { estado: estadoChoices, cidade: cidadeChoices, carregar: carregar };
  }

  /**
   * Busca textual + filtro por atributo data-grupo em tabela já renderizada no servidor.
   */
  function filtroTabela(config) {
    const tabela = document.querySelector(config.tabela);
    if (!tabela) {
      return;
    }

    const linhas = Array.prototype.slice.call(tabela.querySelectorAll('tbody tr[data-grupo]'));
    const busca = config.busca ? document.querySelector(config.busca) : null;
    const abas = config.abas ? document.querySelectorAll(config.abas) : [];
    const contador = config.contador ? document.querySelector(config.contador) : null;
    const vazio = config.vazio ? document.querySelector(config.vazio) : null;
    let grupo = 'todos';

    linhas.forEach(function (linha) {
      linha.dataset.texto = linha.textContent.toLowerCase().replace(/\s+/g, ' ');
    });

    function normalizar(texto) {
      return (texto || '').toLowerCase().trim();
    }

    function aplicar() {
      const termo = normalizar(busca ? busca.value : '');
      let visiveis = 0;

      linhas.forEach(function (linha) {
        const okGrupo = grupo === 'todos' || linha.dataset.grupo === grupo;
        const okTermo = termo === '' || linha.dataset.texto.indexOf(termo) !== -1;
        const mostrar = okGrupo && okTermo;
        linha.classList.toggle('d-none', !mostrar);
        if (mostrar) {
          visiveis++;
        }
      });

      if (contador) {
        contador.textContent = visiveis;
      }
      if (vazio) {
        vazio.classList.toggle('d-none', visiveis > 0);
      }
    }

    if (busca) {
      busca.addEventListener('input', aplicar);
    }

    Array.prototype.forEach.call(abas, function (aba) {
      aba.addEventListener('click', function () {
        Array.prototype.forEach.call(abas, function (outra) { outra.classList.remove('active'); });
        aba.classList.add('active');
        grupo = aba.dataset.filter || 'todos';
        aplicar();
      });
    });

    aplicar();
  }

  window.DMK = {
    baseUrl: baseUrl,
    select: select,
    cidadesPorEstado: cidadesPorEstado,
    filtroTabela: filtroTabela
  };
})();
