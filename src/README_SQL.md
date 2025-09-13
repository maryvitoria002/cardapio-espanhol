# Como usar os arquivos SQL do projeto

## Arquivos disponíveis:

### 1. `dml.sql` - Arquivo principal
- **Descrição**: Contém todas as inserções de dados e consultas de exemplo
- **Uso**: Execute este arquivo para popular o banco com dados de teste
- **Segurança**: Usa `INSERT IGNORE` para evitar conflitos com dados existentes

### 2. `reset_database.sql` - Reset completo (CUIDADO!)
- **Descrição**: Remove TODOS os dados das tabelas
- **Uso**: Execute apenas se quiser recomeçar do zero
- **⚠️ ATENÇÃO**: Apaga todos os dados permanentemente!

## Como executar:

### Opção 1: Executar apenas o DML (RECOMENDADO)
```sql
-- Execute apenas este arquivo:
SOURCE dml.sql;
```
- ✅ Seguro: não apaga dados existentes
- ✅ Adiciona apenas dados novos
- ✅ Pode ser executado múltiplas vezes

### Opção 2: Reset completo + DML
```sql
-- 1. Primeiro execute (APAGA TODOS OS DADOS):
SOURCE reset_database.sql;

-- 2. Depois execute:
SOURCE dml.sql;
```
- ⚠️ CUIDADO: Apaga todos os dados!
- ✅ Garante dados limpos e consistentes
- ✅ Use apenas para desenvolvimento/teste

## Resolução de problemas:

### Erro "Duplicate entry"
- **Causa**: Tentativa de inserir dados que já existem
- **Solução**: O arquivo `dml.sql` já foi corrigido com `INSERT IGNORE`
- **Alternativa**: Use `reset_database.sql` para limpar tudo

### Erro de chave estrangeira
- **Causa**: Tentativa de inserir dados em ordem incorreta
- **Solução**: O arquivo `dml.sql` já desabilita temporariamente as verificações
- **Ordem correta**: categorias → usuários → produtos → carrinho → pedidos → produto_pedido → avaliações → funcionários

## Conteúdo dos dados:

- **10 categorias** de produtos de restaurante
- **20 usuários** com dados completos
- **20 produtos** variados com preços e estoque
- **20 pedidos** com diferentes status
- **20 funcionários** com diferentes níveis de acesso
- **Dados de carrinho e avaliações** para testes

## Consultas incluídas:

1. **Filtros básicos**: WHERE, LIKE, BETWEEN, IN
2. **Funções agregadas**: COUNT, SUM, AVG, MAX, MIN
3. **JOINs**: INNER, LEFT, RIGHT, FULL OUTER
4. **Subconsultas**: correlacionadas e independentes
5. **Verificações**: integridade referencial e estatísticas
