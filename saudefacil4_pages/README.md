# SaúdeFácil – Guia de Instalação

## Requisitos
- XAMPP (Apache + MySQL + PHP 8.0+)
- Navegador moderno

---

## Passo a Passo

### 1. Copiar o projeto
Copie a pasta `saudefacil` para dentro de:
```
C:\xampp\htdocs\saudefacil\   (Windows)
/opt/lampp/htdocs/saudefacil/ (Linux)
```

### 2. Criar o banco de dados
1. Inicie o XAMPP (Apache + MySQL)
2. Abra o navegador em: http://localhost/phpmyadmin
3. Clique em **Importar** (aba superior)
4. Selecione o arquivo `banco_de_dados.sql`
5. Clique em **Executar**

### 3. Configurar a conexão (se necessário)
Edite `includes/conexao.php` se sua configuração for diferente:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // padrão XAMPP sem senha
define('DB_NAME', 'saudefacil');
```

### 4. Acessar o sistema
Abra: http://localhost/saudefacil

---

## Credenciais de Teste

| Perfil    | E-mail                    | Senha     |
|-----------|---------------------------|-----------|
| Admin     | admin@saudefacil.com      | password  |
| (crie seu próprio paciente pelo cadastro) | — | — |

---

## Estrutura de Arquivos

```
saudefacil/
├── banco_de_dados.sql      ← Importar no phpMyAdmin
├── index.php               ← Página inicial
├── login.php               ← Login
├── cadastro.php            ← Cadastro de paciente
├── agendar.php             ← Agendamento de consultas
├── painel.php              ← Painel do paciente
├── unidades.php            ← Listagem de unidades
├── admin.php               ← Painel administrativo
├── logout.php              ← Encerrar sessão
├── css/
│   └── estilo.css          ← Estilos personalizados
├── js/
│   └── main.js             ← Scripts
└── includes/
    ├── conexao.php         ← Configuração do banco
    ├── auth.php            ← Autenticação/sessão
    ├── header.php          ← Cabeçalho HTML
    └── footer.php          ← Rodapé HTML
```

---

## Tecnologias Utilizadas

| Disciplina           | Tecnologia                          |
|---------------------|--------------------------------------|
| Banco de Dados      | MySQL (XAMPP) + PDO + SQL            |
| Linguagem Web       | HTML5 + CSS3 + PHP 8 + JavaScript    |
| Framework           | Bootstrap 5.3 + Bootstrap Icons      |
| Análise de Projeto  | MVC simplificado, CRUD completo      |
  MVC = é um padrão de arquitetura de software que divide uma aplicação em três componentes interligados: os Dados (Model), a Interface (View) e a Lógica de Controle (Controller)
---

## Funcionalidades Implementadas

- ✅ Cadastro e login de pacientes
- ✅ Agendamento de consultas com filtro por especialidade/unidade/data
- ✅ Cancelamento de consultas pelo paciente
- ✅ Painel do paciente com histórico
- ✅ Painel administrativo com estatísticas
- ✅ Listagem de unidades de saúde
- ✅ Dados pré-cadastrados (unidades, médicos, agenda)
- ✅ Design responsivo (mobile/tablet/desktop)
- ✅ Proteção com hash de senhas (bcrypt)
- ✅ Prevenção de SQL Injection (PDO prepared statements)

---

Projeto Integrador – Banco de Dados | Linguagem Web | Análise de Projeto  
Professores: Celso | Marcos | Naura


## Organização atualizada

Os arquivos PHP principais foram movidos para a pasta `pages/`, mantendo `index.php` na raiz como página inicial.

Estrutura:
- `index.php` — página inicial
- `pages/` — páginas do sistema, como login, cadastro, painel, agendamento e administração
- `includes/` — conexão, autenticação, header e footer
- `css/` — arquivos de estilo
- `js/` — scripts

Os caminhos dos includes, CSS, JavaScript e links foram atualizados para o sistema continuar funcionando.
