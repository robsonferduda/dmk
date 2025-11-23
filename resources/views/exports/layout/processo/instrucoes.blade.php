<table>
    <thead>
	    <tr>
            <td>INSTRUÇÕES DE USO DA PLANILHA DE IMPORTAÇÃO DE PROCESSOS</td>
	    </tr>
    </thead>
    <tbody>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>📋 FORMATO DESTA PLANILHA</td>
        </tr>
        <tr>
            <td style="background: #FFF3CD; padding: 10px; font-weight: bold;">
                @if($formato === 'google_sheets')
                    ✓ Esta planilha foi gerada para: GOOGLE SHEETS (Google Planilhas)
                @else
                    ✓ Esta planilha foi gerada para: MICROSOFT EXCEL ou LIBREOFFICE
                @endif
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>⚠️ ATENÇÃO: COMARCA</td>
        </tr>
        @if($formato === 'google_sheets')
            <tr>
                <td><strong>FORMATO GOOGLE SHEETS:</strong> As comarcas aparecem com o prefixo do estado.</td>
            </tr>
            <tr>
                <td>Exemplo: "SC - Florianópolis", "SP - São Paulo", "RJ - Rio de Janeiro"</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><strong>Como usar:</strong></td>
            </tr>
            <tr>
                <td>1. Selecione o ESTADO na coluna "ESTADO" (coluna I)</td>
            </tr>
            <tr>
                <td>2. Na coluna "COMARCA" (coluna J), escolha a comarca que começa com a sigla do estado</td>
            </tr>
            <tr>
                <td>3. As comarcas estão em ordem alfabética para facilitar a busca</td>
            </tr>
        @else
            <tr>
                <td><strong>FORMATO EXCEL/LIBREOFFICE:</strong> Filtro automático de comarca por estado.</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><strong>Como usar:</strong></td>
            </tr>
            <tr>
                <td>1. Primeiro, selecione o ESTADO na coluna "ESTADO" (coluna I)</td>
            </tr>
            <tr>
                <td>2. Depois, clique na coluna "COMARCA" (coluna J)</td>
            </tr>
            <tr>
                <td>3. A lista mostrará APENAS as comarcas do estado selecionado!</td>
            </tr>
            <tr>
                <td>4. Funcionalidade automática: não precisa consultar outras abas</td>
            </tr>
        @endif
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>📊 ABAS DA PLANILHA</td>
        </tr>
        <tr>
            <td>• <strong>Processos</strong>: Aba principal onde você deve preencher os dados dos processos</td>
        </tr>
        <tr>
            <td>• <strong>Varas</strong>: Lista de varas disponíveis</td>
        </tr>
        <tr>
            <td>• <strong>Tipos_de_Serviço</strong>: Lista de tipos de serviço</td>
        </tr>
        <tr>
            <td>• <strong>Cidades</strong>: Lista de comarcas organizadas por estado (consulte esta aba!)</td>
        </tr>
        <tr>
            <td>• <strong>Estados</strong>: Lista de estados</td>
        </tr>
        <tr>
            <td>• <strong>Tipos_de_PROCESSO</strong>: Lista de tipos de processo</td>
        </tr>
        <tr>
            <td>• <strong>Advogados</strong>: Lista de advogados solicitantes</td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>💡 DICAS DE USO</td>
        </tr>
        <tr>
            <td>• Use as listas suspensas (dropdown) sempre que disponíveis</td>
        </tr>
        <tr>
            <td>• Preencha as datas no formato DD/MM/AAAA (ex: 25/12/2024)</td>
        </tr>
        <tr>
            <td>• O campo CLIENTE já vem preenchido automaticamente</td>
        </tr>
        <tr>
            <td>• Não altere o nome das abas ou colunas</td>
        </tr>
        <tr>
            <td>• Não delete as abas auxiliares (Varas, Cidades, Estados, etc.)</td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>💡 IMPORTANTE</td>
        </tr>
        @if($formato === 'google_sheets')
            <tr>
                <td>• Esta planilha foi otimizada para Google Sheets</td>
            </tr>
            <tr>
                <td>• Se usar no Excel/LibreOffice, funcionará mas sem filtro automático de comarca</td>
            </tr>
            <tr>
                <td>• Para gerar uma versão otimizada para Excel/LibreOffice, gere a planilha novamente selecionando a opção adequada</td>
            </tr>
        @else
            <tr>
                <td>• Esta planilha foi otimizada para Excel e LibreOffice</td>
            </tr>
            <tr>
                <td>• Se usar no Google Sheets, o filtro de comarca pode não funcionar corretamente</td>
            </tr>
            <tr>
                <td>• Para gerar uma versão otimizada para Google Sheets, gere a planilha novamente selecionando a opção adequada</td>
            </tr>
        @endif
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td>❌ OBSERVAÇÕES</td>
        </tr>
        <tr>
            <td>• <strong>Excel:</strong> Pode exibir alerta de segurança ao abrir - clique em "Habilitar Edição"</td>
        </tr>
        <tr>
            <td>• <strong>Formato correto:</strong> Sempre use a planilha gerada para a plataforma que você vai utilizar</td>
        </tr>
        <tr>
            <td>• <strong>Compatibilidade cruzada:</strong> Planilhas geradas para um formato podem ter funcionalidade limitada em outras plataformas</td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td style="text-align: center; color: #4472C4; font-weight: bold;">Em caso de dúvidas, entre em contato com o suporte técnico.</td>
        </tr>
    </tbody>
</table>
