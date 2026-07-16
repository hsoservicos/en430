/**
 * app.js — Sistema de Avaliação EN_430
 * Versão PHP + JavaScript
 * 
 * Funcionalidades:
 *   1. Máscaras de entrada (Telefone, CPF, CEP, Data)
 *   2. Enter para navegar/submeter formulários
 *   3. Auto-focus no primeiro campo
 *   4. Seleção visual de dificuldade
 *   5. Contador de questões respondidas
 *   6. Micro-interações e animações
 */

(function() {
  'use strict';

  // ─── MÁSCARAS DE ENTRADA ──────────────────────────────────
  function aplicarMascara(input, formatarFn) {
    function processar() {
      const valor = input.value.replace(/\D/g, '');
      input.value = formatarFn(valor);
    }
    input.addEventListener('input', processar);
    input.addEventListener('paste', function() {
      setTimeout(processar, 10);
    });
  }

  // Telefone: (XX) XXXXX-XXXX
  function formatarTelefone(digitos) {
    const v = digitos.slice(0, 11);
    let f = '';
    if (v.length > 0) f = '(' + v.slice(0, 2);
    if (v.length > 2) f += ') ' + v.slice(2, 7);
    if (v.length > 7) f += '-' + v.slice(7);
    return f;
  }

  // CPF: 000.000.000-00
  function formatarCPF(digitos) {
    const v = digitos.slice(0, 11);
    let f = '';
    if (v.length > 0) f = v.slice(0, 3);
    if (v.length > 3) f += '.' + v.slice(3, 6);
    if (v.length > 6) f += '.' + v.slice(6, 9);
    if (v.length > 9) f += '-' + v.slice(9);
    return f;
  }

  // CEP: 00000-000
  function formatarCEP(digitos) {
    const v = digitos.slice(0, 8);
    let f = '';
    if (v.length > 0) f = v.slice(0, 5);
    if (v.length > 5) f += '-' + v.slice(5);
    return f;
  }

  // Data: DD/MM/AAAA
  function formatarData(digitos) {
    const v = digitos.slice(0, 8);
    let f = '';
    if (v.length > 0) f = v.slice(0, 2);
    if (v.length > 2) f += '/' + v.slice(2, 4);
    if (v.length > 4) f += '/' + v.slice(4);
    return f;
  }

  // Registrar máscaras via data-mask
  const mascaras = {
    'telefone': formatarTelefone,
    'cpf': formatarCPF,
    'cep': formatarCEP,
    'data': formatarData
  };

  Object.keys(mascaras).forEach(function(tipo) {
    document.querySelectorAll('[data-mask="' + tipo + '"]').forEach(function(input) {
      aplicarMascara(input, mascaras[tipo]);
    });
  });

  // ─── ENTER PARA NAVEGAR/SUBMETER ──────────────────────────
  function initEnterNavigation() {
    const form = document.getElementById('form-avaliacao');
    
    if (form) {
      initEnterAvaliacao(form);
    }

    // Formulários com data-enter-submit
    document.querySelectorAll('form[data-enter-submit]').forEach(function(f) {
      if (f.id === 'form-avaliacao') return;
      
      const inputs = Array.from(f.querySelectorAll('input:not([type=hidden]):not([type=submit]):not([type=button]), textarea, select'));
      
      inputs.forEach(function(input, index) {
        input.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (index < inputs.length - 1) {
              inputs[index + 1].focus();
            } else {
              submitForm(f);
            }
          }
        });
      });
    });
  }

  function initEnterAvaliacao(form) {
    const questoes = form.querySelectorAll('.questao-card');
    
    questoes.forEach(function(card, index) {
      const options = card.querySelectorAll('.q-opcoes li');
      
      options.forEach(function(li) {
        li.setAttribute('tabindex', '0');
        
        li.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            // Selecionar esta opção
            const radio = li.querySelector('input[type="radio"]');
            if (radio) {
              radio.checked = true;
              radio.dispatchEvent(new Event('change'));
            }
            li.click();
            
            // Avançar para próxima questão
            if (index < questoes.length - 1) {
              const nextCard = questoes[index + 1];
              const firstOption = nextCard.querySelector('.q-opcoes li');
              if (firstOption) {
                firstOption.focus();
                nextCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
              }
            }
          }
        });
      });
    });

    // Enter + Shift para finalizar
    const lastQuestion = questoes[questoes.length - 1];
    if (lastQuestion) {
      lastQuestion.querySelectorAll('.q-opcoes li').forEach(function(li) {
        li.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' && e.shiftKey) {
            e.preventDefault();
            if (confirm('Tem certeza que deseja finalizar a avaliação? Questões não respondidas serão consideradas erradas.')) {
              submitForm(form);
            }
          }
        });
      });
    }
  }

  function submitForm(form) {
    const btn = form.querySelector('button[type="submit"]');
    if (btn) {
      btn.click();
    } else {
      form.submit();
    }
  }

  initEnterNavigation();

  // ─── AUTO-FOCUS ───────────────────────────────────────────
  document.querySelectorAll('[data-autofocus]').forEach(function(el) {
    el.focus();
  });

  // ─── EFEITO DE CLIQUE NOS FILTROS ─────────────────────────
  document.querySelectorAll('.filter-opt').forEach(function(opt) {
    opt.addEventListener('click', function() {
      this.closest('.filter-options').querySelectorAll('.filter-opt').forEach(function(o) {
        o.classList.remove('selected');
      });
      this.classList.add('selected');
      const radio = this.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });

  // ─── ANIMAÇÃO DE BARRAS DE PROGRESSO ──────────────────────
  function animateBars() {
    document.querySelectorAll('.bar-fill').forEach(function(bar) {
      const width = bar.style.width;
      bar.style.width = '0%';
      setTimeout(function() {
        bar.style.width = width;
      }, 100);
    });
  }

  // Observar quando as barras entram na tela
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          animateBars();
          observer.unobserve(entry.target);
        }
      });
    });
    
    document.querySelectorAll('.bar').forEach(function(bar) {
      observer.observe(bar);
    });
  } else {
    // Fallback: animar após carregar
    setTimeout(animateBars, 500);
  }

  // ─── CONFIRMAÇÃO DE AÇÕES IMPORTANTES ─────────────────────
  document.querySelectorAll('[data-confirm]').forEach(function(el) {
    el.addEventListener('click', function(e) {
      if (!confirm(this.getAttribute('data-confirm'))) {
        e.preventDefault();
      }
    });
  });

  console.log('📝 Sistema de Avaliação EN_430 — JS carregado ✅');
})();
