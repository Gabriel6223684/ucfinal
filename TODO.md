# TODO - Login/Register refactor

- [ ] Entender comportamento atual de login/register (rotas, controllers, middleware e JS)
- [ ] Corrigir controllers: `Login` (usar nomes corretos de métodos), implementar `Register` (criar `Register::register`/`preregister`/etc conforme rota)
- [ ] Criar `register.js` separado a partir do código/HTML atual
- [ ] Ajustar `login.js` para enviar corretamente para rota de login (sem endpoint “/” aleatório)
- [ ] Ajustar `routes.php`: criar rotas explícitas para `POST /authentication/auth` e `POST /authentication/preregister` (ou conforme necessidade), e rotas GET para `/register` se aplicável
- [ ] Ajustar `Middleware`: impedir loops (ex.: redirect para /login ao acessar endpoints POST), e tratar caminhos `/authentication/*` (API) com regra `api()`
- [ ] Atualizar HTML `login.html` para incluir `register.js` e garantir IDs/names compatíveis com back-end
- [ ] Rodar testes/linters quando aplicável (pest/phpstan)

