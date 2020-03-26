# WooCommerce AZPAY - Plugin para pagamentos
Woocommerce 3.6.4 ou superior

# Pré-requisitos
 - php 7.1 ou superior
 - Wordpress 5.2.2
 - Woocommerce 3.6.4

# Instalação
> 1. Extraia os arquivos desse repositório.
> 2. Faça a compactação da pasta "azpay-woocommerce" em modo ZIP
> 3. No painel admin do Wordpress, acessar o menu Plugins > Adicionar novo
> 4. Botão Enviar plugin > Escolher arquivo
> 5. Faça o upload e instalação do arquivo azpay-woocommerce.zip
> 6. Em configurações > Links Permanentes > Mudar para "Dia e nome"
> 7. Instale o plugin complementar chamado WooCommerce Extra Checkout Fields for Brazil
 


# Configuração dos meios de pagamento
> Ao instalar o plugin, as opções de pagamento ficarão disponíveis em: WooCommerce > Configurações > Pagamentos.
> - Azpay - Cartão de Crédito
> - Azpay - Cartão de Débito
> - Azpay - Boleto
> - Azpay - Transferência

> Entrar em Gerenciar, na opção de pagamento, adicionar o Merchant ID e Merchant Key e configurar outras opções.

# Azpay - Cartão de Crédito
> - **Título** - Título exibido para o cliente ao escolher opção de pagamento
> - **Descrição** - Descrição exibido para o cliente ao escolher opção de pagamento
> - **Ambiente** - Define se o ambiente de Teste ou Produção pelo gateway
> - **Soft Descriptor** - Descrição que será exibida na fatura do cartão
> - **Meio de pagamento** - Operador de pagamento pelo gateway
> - **Antifraude** - Ativa o uso do antifraude pelo gateway / Seleciona opção de antifraude
> - **Capturar** - Ativa captura automático pelo gateway, caso não marcado, a captura deve ser feita manual
> - **Parcela Mínima** - Valor mínimo que uma parcela pode ter
> - **Parcelar até** - Quantidade máxima de parcelas
> - **Taxa de Juros(%)** - Taxa de juros cobradas por parcela
> - **Cobrar juros a partir de** - Cobrar taxa de juros a partir da quantidade de parcelas
> - **Valor mínimo para exibição** - Valor mínimo de compra para exibir a opção de pagamento

# Azpay - Cartão de Débito
> - **Título** - Título exibido para o cliente ao escolher opção de pagamento
> - **Descrição** - Descrição exibido para o cliente ao escolher opção de pagamento
> - **Ambiente** - Define se o ambiente de Teste ou Produção pelo gateway
> - **Soft Descriptor** - Descrição que será exibida na fatura do cartão
> - **Meio de pagamento** - Operador de pagamento pelo gateway
> - **Antifraude** - Ativa o uso do antifraude pelo gateway / Seleciona opção de antifraude
> - **Desconto Débito(%)** - Desconto se usar opção de pagamento
> - **Valor mínimo para exibição** - Valor mínimo de compra para exibir a opção de pagamento

# Azpay - Boleto
> - **Título** - Título exibido para o cliente ao escolher opção de pagamento
> - **Descrição** - Descrição exibido para o cliente ao escolher opção de pagamento
> - **Ambiente** - Define se o ambiente de Teste ou Produção pelo gateway
> - **Soft Descriptor** - Descrição que será exibida na fatura do cartão
> - **Meio de pagamento** - Operador de pagamento pelo gateway
> - **Desconto Boleto(%)** - Desconto se usar opção de pagamento
> - **Vencimento em (dias)** - Quantidade de dias para vencimento do boleto a partir da data da compra
> - **Valor mínimo para exibição** - Valor mínimo de compra para exibir a opção de pagamento

# Azpay - Transferência Bancária
> - **Título** - Título exibido para o cliente ao escolher opção de pagamento
> - **Descrição** - Descrição exibido para o cliente ao escolher opção de pagamento
> - **Ambiente** - Define se o ambiente de Teste ou Produção pelo gateway
> - **Soft Descriptor** - Descrição que será exibida na fatura do cartão
> - **Meio de pagamento** - Operador de pagamento pelo gateway
> - **Desconto Transferência(%)** - Desconto se usar opção de pagamento
> - **Valor mínimo para exibição** - Valor mínimo de compra para exibir a opção de pagamento

## Demonstração - testes 

 
Url de demonstração:
http://52.67.44.131:8189/

### Docker
> Docker Image 
 `docker push l2go/woocommerce:latest`

> Via Docker Compose
 `docker-compose up`

## Preview

![Logo](https://www.azpay.com.br/blog/wp-content/uploads/2020/03/Captura-de-Tela-2020-03-25-a%CC%80s-18.34.18-278x300.png)
![Logo](https://www.azpay.com.br/blog/wp-content/uploads/2020/03/screencapture-52-67-44-131-8189-2020-03-25-19_31_58-e1585175809833-225x300.png)





