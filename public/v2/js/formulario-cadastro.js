(function () {
  'use strict';

  const form = document.getElementById('form-cadastro');
  if (!form) {
    return;
  }

  const urlCidades = form.dataset.cidades;
  const siglas = JSON.parse(form.dataset.estados || '{}');
  const cacheCidades = {};
  const LIMITE_VISIVEL = 24;

  function cidadesDoEstado(estado) {
    if (!cacheCidades[estado]) {
      cacheCidades[estado] = fetch(urlCidades + '/' + encodeURIComponent(estado), { headers: { Accept: 'application/json' } })
        .then(function (r) {
          if (!r.ok) {
            throw new Error('HTTP ' + r.status);
          }
          return r.json();
        })
        .catch(function (erro) {
          delete cacheCidades[estado];
          throw erro;
        });
    }
    return cacheCidades[estado];
  }

  function normalizar(texto) {
    return String(texto || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase().trim();
  }

  /* Máscaras */
  const mascaras = {
    documento: function (d) {
      d = d.slice(0, 14);
      if (d.length <= 11) {
        return d.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
      }
      return d.replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    },
    telefone: function (d) {
      d = d.slice(0, 11);
      if (d.length <= 2) {
        return d ? '(' + d : '';
      }
      const meio = d.length === 11 ? 5 : 4;
      return '(' + d.slice(0, 2) + ') ' + d.slice(2, 2 + meio) + (d.length > 2 + meio ? '-' + d.slice(2 + meio) : '');
    },
    cep: function (d) {
      d = d.slice(0, 8);
      return d.length > 5 ? d.slice(0, 5) + '-' + d.slice(5) : d;
    }
  };

  form.querySelectorAll('[data-mascara]').forEach(function (campo) {
    const aplicar = function () {
      campo.value = mascaras[campo.dataset.mascara](campo.value.replace(/\D+/g, ''));
    };
    campo.addEventListener('input', aplicar);
    if (campo.value) {
      aplicar();
    }
  });

  /* Estado → cidade */
  function seletorCidade(estadoEl, cidadeEl, rotulo) {
    const choices = DMK.select(cidadeEl, { noResultsText: 'Nenhuma cidade encontrada' });

    function opcoes(lista, valor) {
      return [{ value: '', label: rotulo, selected: !valor }].concat(lista.map(function (c) {
        return { value: String(c.id), label: c.nome, selected: String(c.id) === String(valor) };
      }));
    }

    function carregar(estado, valor) {
      choices.clearStore();
      if (!estado) {
        choices.setChoices([{ value: '', label: 'Selecione o estado primeiro', selected: true }], 'value', 'label', true);
        choices.disable();
        return Promise.resolve([]);
      }
      choices.enable();
      choices.setChoices([{ value: '', label: 'Carregando cidades...', selected: true, disabled: true }], 'value', 'label', true);

      return cidadesDoEstado(estado)
        .then(function (lista) {
          choices.clearStore();
          choices.setChoices(opcoes(lista, valor), 'value', 'label', true);
          return lista;
        })
        .catch(function () {
          choices.clearStore();
          choices.setChoices([{ value: '', label: 'Não foi possível carregar as cidades. Tente de novo.', selected: true }], 'value', 'label', true);
          return [];
        });
    }

    estadoEl.addEventListener('change', function () {
      carregar(estadoEl.value, '');
    });
    carregar(estadoEl.value, cidadeEl.dataset.selecionada || '');

    return { choices: choices, carregar: carregar };
  }

  const estadoEl = document.getElementById('estado');
  const cidade = seletorCidade(estadoEl, document.getElementById('cidade'), 'Selecione a cidade');
  seletorCidade(document.getElementById('origem_estado'), document.getElementById('origem'), 'Selecione a comarca de origem');
  DMK.select(document.getElementById('banco'), { noResultsText: 'Nenhum banco encontrado' });

  /* CEP → endereço (ViaCEP) */
  const cepEl = document.getElementById('cep');
  const cepSituacao = document.getElementById('cep-situacao');
  let ultimoCep = cepEl.value.replace(/\D+/g, '');

  cepEl.addEventListener('input', function () {
    const cep = cepEl.value.replace(/\D+/g, '');
    if (cep.length !== 8 || cep === ultimoCep) {
      return;
    }
    ultimoCep = cep;
    cepSituacao.textContent = 'Buscando endereço...';

    fetch('https://viacep.com.br/ws/' + cep + '/json/')
      .then(function (r) { return r.json(); })
      .then(function (end) {
        if (end.erro) {
          cepSituacao.textContent = 'CEP não encontrado. Preencha o endereço manualmente.';
          return;
        }
        cepSituacao.textContent = 'Endereço preenchido pelo CEP. Confira e informe o número.';
        if (end.logradouro) {
          document.getElementById('logradouro').value = end.logradouro;
        }
        if (end.bairro) {
          document.getElementById('bairro').value = end.bairro;
        }

        const estado = Object.keys(siglas).find(function (id) { return siglas[id] === end.uf; });
        if (!estado) {
          return;
        }
        estadoEl.value = estado;
        cidade.carregar(estado, '').then(function (lista) {
          const alvo = normalizar(end.localidade);
          const achada = lista.find(function (c) { return normalizar(c.nome) === alvo; });
          if (achada) {
            cidade.choices.setChoiceByValue(String(achada.id));
          }
        });
        document.getElementById('numero').focus();
      })
      .catch(function () {
        cepSituacao.textContent = 'Não foi possível consultar o CEP. Preencha o endereço manualmente.';
      });
  });

  /* Advogado x preposto: para preposto a OAB é opcional */
  function alternarOab() {
    const atuacao = form.querySelector('input[name="atuacao"]:checked');
    const preposto = atuacao && atuacao.value === 'preposto';
    form.querySelectorAll('[data-oab-opcional]').forEach(function (el) {
      el.classList.toggle('d-none', !preposto);
    });
  }
  form.querySelectorAll('input[name="atuacao"]').forEach(function (el) {
    el.addEventListener('change', alternarOab);
  });
  alternarOab();

  /* Comarcas de atendimento */
  const selecionadas = new Map();
  const abertos = new Set();
  JSON.parse(form.dataset.comarcas || '[]').forEach(function (c) {
    selecionadas.set(String(c.id), { id: String(c.id), nome: c.nome, uf: c.uf });
  });

  const lista = document.getElementById('comarcas-lista');
  const campos = document.getElementById('comarcas-campos');
  const comarcaEstado = document.getElementById('comarca_estado');
  const comarcaCidade = seletorCidade(comarcaEstado, document.getElementById('comarca_cidade'), 'Selecione a comarca');
  const botaoTodas = document.getElementById('comarca-todas');

  function elemento(tag, classe, texto) {
    const el = document.createElement(tag);
    if (classe) {
      el.className = classe;
    }
    if (texto !== undefined) {
      el.textContent = texto;
    }
    return el;
  }

  function desenhar() {
    lista.innerHTML = '';
    campos.value = Array.from(selecionadas.keys()).join(',');

    if (!selecionadas.size) {
      lista.appendChild(elemento('p', 'small text-muted mb-0', 'Nenhuma comarca adicional. Se atende apenas na comarca de origem, pode deixar assim.'));
      return;
    }

    const porUf = {};
    selecionadas.forEach(function (c) {
      (porUf[c.uf] = porUf[c.uf] || []).push(c);
    });

    Object.keys(porUf).sort().forEach(function (uf) {
      const cidades = porUf[uf].sort(function (a, b) { return a.nome.localeCompare(b.nome, 'pt-BR'); });
      const grupo = elemento('div', 'dmk-comarcas-grupo');
      const topo = elemento('div', 'dmk-comarcas-topo');
      topo.appendChild(elemento('strong', '', uf));
      topo.appendChild(elemento('span', 'text-muted small', cidades.length + (cidades.length === 1 ? ' comarca' : ' comarcas')));

      const remover = elemento('button', 'btn btn-link btn-sm text-danger p-0 ms-auto', 'Remover todas');
      remover.type = 'button';
      remover.addEventListener('click', function () {
        cidades.forEach(function (c) { selecionadas.delete(c.id); });
        desenhar();
      });
      topo.appendChild(remover);
      grupo.appendChild(topo);

      const aberto = abertos.has(uf) || cidades.length <= LIMITE_VISIVEL;
      const chips = elemento('div', 'dmk-comarcas-chips');
      (aberto ? cidades : cidades.slice(0, LIMITE_VISIVEL)).forEach(function (c) {
        const chip = elemento('span', 'dmk-comarca-chip', c.nome);
        const x = elemento('button', 'btn-close');
        x.type = 'button';
        x.setAttribute('aria-label', 'Remover ' + c.nome);
        x.addEventListener('click', function () {
          selecionadas.delete(c.id);
          desenhar();
        });
        chip.appendChild(x);
        chips.appendChild(chip);
      });
      if (cidades.length > LIMITE_VISIVEL) {
        const alternar = elemento('button', 'btn btn-link btn-sm p-0', aberto ? 'Mostrar menos' : 'Ver todas (' + cidades.length + ')');
        alternar.type = 'button';
        alternar.addEventListener('click', function () {
          if (abertos.has(uf)) {
            abertos.delete(uf);
          } else {
            abertos.add(uf);
          }
          desenhar();
        });
        chips.appendChild(alternar);
      }
      grupo.appendChild(chips);
      lista.appendChild(grupo);
    });
  }

  function adicionar(estado, cidades) {
    const uf = siglas[estado];
    cidades.forEach(function (c) {
      selecionadas.set(String(c.id), { id: String(c.id), nome: c.nome, uf: uf });
    });
    desenhar();
  }

  comarcaEstado.addEventListener('change', function () {
    botaoTodas.disabled = !comarcaEstado.value;
  });

  document.getElementById('comarca-adicionar').addEventListener('click', function () {
    const estado = comarcaEstado.value;
    const id = comarcaCidade.choices.getValue(true);
    if (!estado || !id) {
      return;
    }
    cidadesDoEstado(estado).then(function (cidades) {
      adicionar(estado, cidades.filter(function (c) { return String(c.id) === String(id); }));
      comarcaCidade.choices.setChoiceByValue('');
    });
  });

  botaoTodas.addEventListener('click', function () {
    const estado = comarcaEstado.value;
    if (!estado) {
      return;
    }
    cidadesDoEstado(estado).then(function (cidades) {
      if (window.confirm('Adicionar as ' + cidades.length + ' comarcas de ' + siglas[estado] + '?')) {
        adicionar(estado, cidades);
      }
    });
  });

  desenhar();

  form.addEventListener('submit', function () {
    const botao = document.getElementById('enviar');
    botao.disabled = true;
    botao.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';
  });
})();
