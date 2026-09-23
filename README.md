# Central de Atendimento Emergencial

Sistema web desenvolvido em PHP e MySQL/MariaDB para gerenciamento e triagem de solicitações emergenciais com controle de acesso administrativo.

## 📋 Sobre o Projeto

O projeto consiste em uma **Central de Atendimento Emergencial** onde:
* O público geral pode enviar solicitações de emergência sem necessidade de login, categorizadas por níveis de prioridade (`Normal`, `Importante`, `Urgente`).
* A equipe de administração possui uma área restrita para visualizar, assumir e atualizar o status das chamadas (`Pendente`, `Em Atendimento`, `Resolvido`).


## 🛠️ Requisitos Prévios

Certifique-se de ter os seguintes recursos instalados em seu ambiente local:
* **Servidor Web:** Apache (Linux) ou XAMPP/Wampp (Windows)
* **Linguagem:** PHP 8.0 ou superior
* **Banco de Dados:** MySQL ou MariaDB
* **Controle de Versão:** Git


## 🚀 Passo a Passo para Configuração Local

Siga as etapas abaixo para configurar o ambiente de desenvolvimento em sua máquina.

### 1. Clonar o Repositório

Abra o terminal no diretório raiz do seu servidor web (ex: `/var/www/` no Linux ou `C:/xampp/htdocs/` no Windows) e execute:

```bash
git clone [https://github.com/mariafernanda0011/hackathon-central-atendimento.git](https://github.com/mariafernanda0011/hackathon-central-atendimento.git) central.local
```
```bash
cd central.local
```
---

### 2. Criar o Banco de Dados

Importe o arquivo `schema.sql` para criar o banco de dados `central_atendimento` e suas respectivas tabelas.

* **Opção A — Linux (Terminal):**
  ```bash
  sudo mysql -u root < schema.sql
  ```
  *(Caso utilize MariaDB, pode executar `sudo mariadb -u root < schema.sql`)*

* **Opção B — XAMPP / phpMyAdmin:**
  1. Acesse `http://localhost/phpmyadmin`
  2. Clique na aba **Importar**.
  3. Selecione o arquivo `schema.sql` e clique em **Executar**.

---

### 3. Configurar Conexão com o Banco (`database/database_setup.php`)

Por razões de segurança, as credenciais de acesso ao banco não são versionadas no Git. Cada desenvolvedor deve criar seu próprio arquivo de conexão local a partir do modelo disponibilizado.

1. Duplique o arquivo de exemplo dentro da pasta `database/`

2. Se necessário, abra o arquivo `database/database_setup.php` e ajuste o usuário e a senha de acordo com o seu ambiente local:

---

### 4. Criar Administrador Local de Testes

Como a tabela de administradores é iniciada vazia, crie um arquivo temporário chamado `seeder.php` na raiz do projeto para gerar o primeiro usuário de testes na sua máquina.

1. Crie o arquivo `seeder.php` na raiz com o seguinte conteúdo:

```php
<?php
require_once __DIR__ . '/config/database.php';

$admins = [
    ['Administrador Geral', 'admin@central.local',     'admin123',  'geral', null],
    ['Central Bombeiros',   'bombeiros@central.local', 'setor123',  'setor', 'bombeiros'],
    ['Central Polícia',     'policia@central.local',   'setor123',  'setor', 'policia'],
    ['Central SAMU',        'samu@central.local',      'setor123',  'setor', 'samu'],
];

foreach ($admins as [$nome, $email, $senha, $tipo, $slugSetor]) {
    $setorId = null;
    if ($slugSetor) {
        $q = $pdo->prepare("SELECT id FROM setores WHERE slug = :s");
        $q->execute([':s' => $slugSetor]);
        $setorId = $q->fetchColumn() ?: null;
    }

    $stmt = $pdo->prepare("INSERT INTO administradores (nome, email, senha, tipo, setor_id)
                           VALUES (:nome, :email, :senha, :tipo, :setor_id)
                           ON DUPLICATE KEY UPDATE nome=VALUES(nome), senha=VALUES(senha),
                                                   tipo=VALUES(tipo), setor_id=VALUES(setor_id)");
    $stmt->execute([
        ':nome'     => $nome,
        ':email'    => $email,
        ':senha'    => password_hash($senha, PASSWORD_BCRYPT),
        ':tipo'     => $tipo,
        ':setor_id' => $setorId,
    ]);
    echo "OK: $email ($tipo)\n";
}

```

2. Execute o comando no terminal a partir da raiz do projeto para cadastrar o usuário: 

```bash
php seeder.php
```
---

### 5. Configurar Virtual Host no Apache (Apenas Linux)

Para rodar o projeto usando o domínio local `http://central.local`:

1. **Criar o arquivo de configuração do Virtual Host:**
   ```bash
   sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/central.local.conf
   sudo nano /etc/apache2/sites-available/central.local.conf
   ```

2. **Inserir as configurações:**
   ```apache
   <VirtualHost *:80>
       ServerName central.local
       ServerAlias www.central.local
       DocumentRoot /var/www/central.local/public

       <Directory "/var/www/central.local/public">
           Options Indexes FollowSymLinks MultiViews
           AllowOverride All
           Require all granted
       </Directory>

       ErrorLog ${APACHE_LOG_DIR}/error.log
       CustomLog ${APACHE_LOG_DIR}/access.log combined
   </VirtualHost>
   ```

3. **Ativar o Virtual Host e reiniciar o Apache:**
   ```bash
   sudo a2ensite central.local.conf
   sudo systemctl reload apache2
   ```

4. **Mapear o domínio no `/etc/hosts`:**
   ```bash
   sudo nano /etc/hosts
   ```
   Adicione a linha:
   ```text
   127.0.1.1 central.local
   ```

Acesse a aplicação no navegador em: `http://central.local`

---

## 🔒 Regras de Versionamento e Segurança (Git)

Para evitar conflitos de código e vazamento de informações locais, observe as seguintes regras:

1. **Arquivos Ignorados:** Nunca remova ou force o envio dos arquivos mapeados no `.gitignore` (`config/database.php`, `seeder.php`, `.env`).
2. **Branches:** Crie branches separadas para trabalhar em novas funcionalidades antes de fazer o *merge* na branch `main`:
   ```bash
   git checkout -b feature/nome-da-sua-tarefa
   ```
3. **Commits:** Faça commits pequenos e objetivos descrevendo claramente as alterações realizadas.
