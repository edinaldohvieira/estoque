=== Estoque ===
Contributors: edinaldohvieira
Donate link: https://edinaldohvieira.com/financeiro/doacao/
Tags: produto, estoque
Requires at least: 4.4
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: 0.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Controle de estoque simples com entrada e saida de produtos.

== Description ==

O plugin Estoque  permite cadastrar uma lista de produtos que depois pode-se utilizar um formulario de entrada de produtos e uma tela de saída de produtos. Usando essas duas telas o plugin contabiliza na listagem de produtos a quantidade existente de cada produto.


== Installation ==

Proceda como de custume pesquisando plugin ou faca o upload do plugin
This section describes how to install the plugin and get it working.

e.g.

1. Proceda com o upload da pasta do plugin `estoque` para o diretorio `/wp-content/plugins/` do seu site onde deseja instalar
2. Ative o plugin acessando a aba 'Plugins' no menu do WordPress
3. Crie uma pagina e cole o shortcode [estoque_listagem]
4. Cria uma pagina e cole o shortcode [estoque_entrada]
5. Crie uma pagina e cole o shortcode [estoque_saida]

== Frequently Asked Questions ==

= Usa-se tabela do db ou post-type para armazenar os registros? =

O plugin cria 3 tabelas na ativacao. Uma tabela para a listagem do estoque, uma tabela para deixar resgistrado as entradas de estoque e outra tabela para deixar registrado as saidas do estoque.

= Como e feito o acrescimo e deducao do estoque? =

A deducao e acrescimo de produto e feito atravez de trigger dentro do banco de dados.

== Screenshots ==

1. Imagem 1.
2. Imagem 2
3. Imagem 3

== Changelog ==

= 0 1.0 =
* Liberacao do plugin contendo as opcoes mais basicas.

== Upgrade Notice ==

= 0.1.1 =

