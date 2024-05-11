# Rest-API-Laravel

As APIs REST se comunicam por meio de solicitações HTTP para executar funções padrão de banco de dados, como criar, ler, atualizar e excluir registros (também conhecidos como CRUD) em um recurso.
Por exemplo, uma API REST usaria uma solicitação GET para recuperar um registro. Uma solicitação POST cria um novo registro. Uma solicitação PUT atualiza um registro, e uma solicitação DELETE exclui um. Todos os métodos HTTP podem ser usados em chamadas de API. Uma API REST bem projetada é semelhante a um site em execução em um navegador web com funcionalidade HTTP integrada.
Para saber mais: [O que é uma API REST? | IBM](https://www.ibm.com/br-pt/topics/rest-apis)

Para criar esta api rest nós iremos simular um ecommerce, onde o administradr poderá 

- Visualizar produtos existentes
- Criar novos produtos
- Atualizar seus produtos
- Deletar produtos

Requisitos  

- Composer instalado no computador (Obrigatorio). Para instalar o composer: [Composer (getcomposer.org)](https://getcomposer.org/download/)
- Editor de código, neste tutorial usaremos o Visual Studio Code (Obrigatório). Para instalar o VS code: [Visual Studio Code - Code Editing. Redefined](https://code.visualstudio.com/)
- Banco de dados e gerenciador de banco de dados, neste tutorial utilizaremos o mysqli e o PhpMyadmin do Xampp. Para intalar o Xampp: [Download XAMPP (apachefriends.org)](https://www.apachefriends.org/download.html)
- Postman  para testar a API (Opcional). Para instalar o Postman: [Download Postman | Get Started for Free](https://www.postman.com/downloads/)

1. Para iniciar, vamos criar um projeto laravel no nosso computador, através do comando 

```
composer create-project laravel/laravel ecommerce-example-api
```

2. Entrar na pasta do projeto pelo terminal

```
cd ecommerce-example-api
```

3. Abrir o projeto no editor de código 

```
code .
```

4. Conectar ao banco de dados no arquivo “.env” da raiz da pasta do laravel 

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

5. Criar Model do produto juntamente com a migration, no CMD:

```
php artisan make:model Produto -m
```

6. Adicionar campos ao Schema do produto no arquivo “database/migrations/…create_produtos_table.php”. Aqui o nosso produto terá os atributos, nome, descrição e imagem, além dos campos padrões de id e timestamps:

```php
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string("nome");
            $table->string("descricao");
            $table->string("imagem");
            $table->timestamps();
        });
    }
   
```

7. Rodar migration, após isso nossa tabela de produtos estará criada:

```
php artisan migrate
```

8. Editar Model da tabela de produtos, para informar que os atributos “nome, descricao e imagem” podem ser ser atibuídos aos criar ou atualizar nosso modelo, faça isto no arquivo “app/Models/Produto.php”:

```php
class Produto extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'descricao', 'imagem'];
}
```

 

9. Criar controller dos produtos, no cmd rodar:

```
php artisan make:controller Api/ProdutoController
```

10. Adicinar rota no arquivo “routes/api.php”;

```php
use App\Http\Controllers\Api\ProdutoController;

Route::group([], function () {
    Route::apiResource('produtos', ProdutoController::class);
});

```

11. Criar o método de index do controller, que dará acesso a todos os produtos da aplicação no arquivo “app/Http/Controllers/Api/ProdutoController.php”:

```
class ProdutoController extends Controller
{
    public function index(){
        return response()->json('Index Produtos');
    }
}
```

Agora quando acessamos “[localhost/ecommerce-example-api/public/api/produtos](http://localhost/ecommerce-example-api/public/api/produtos)” obtemos: 

**"Index Produtos”** 

como resultado.

12. Criar request (Referente ao envio de dados do cliente para o servidor) para armazenar os produtos no banco de dados, digitar no CMD: 

```
php artisan make:request StoreProdutoRequest
```

13. Permitir o usuário inserir dados, aqui permitiremos que qualquer usuário faça requests, mas em uma aplicação real nós utilisariamos de lógicas para permitira que apenas usuários com permissão fizessem requests. No arquivo “app/Http/Requests/StoreProdutoRequest.php” vamos substituir o false pelo true: 

 

```
    public function authorize(): bool
    {
        return true;
    }
```

14. Criar regras de validação para a inserção de novos dados: app\Http\Requests\StoreProdutoRequest:

```php
    public function rules(): array
    {
        return [
            'nome' => ['required'],
            'descricao' => ['required'],
            'imagem' => ['required']
        ];
    }
```

15. Criar método para armazenamento dos dados, no nosso controller adicionar: 

```
use App\Http\Requests\StoreProdutoRequest;

    public function store(StoreProdutoRequest $request){
        Produto::create($request->validated());
        return response()->json('Produtos criados');
    }
```

Agora, ao enviarmos um json para a rota “[localhost/ecommerce-example-api/public/api/produtos](http://localhost/ecommerce-example-api/public/api/produtos)”, com o método POST, passando as informações do produto

```
{
	"nome": "Produto 1",
    "descricao": "Primeiro produto do ecommerce",
    "imagem": "./produto1.png"
}
```

 salvaremos elas no banco de dados.

16. Criar controller de update, para atualizar os nossos dados. Agora, ao enviarmos um json para a rota “[localhost/ecommerce-example-api/public/api/produtos](http://localhost/ecommerce-example-api/public/api/produtos)/{id}”, com o método PUT, passando as informações do produto novo: 

```php
{
	"nome": "Produto 2",
    "descricao": "Segundo produto do ecommerce",
    "imagem": "./produto2.png"
}   
```

Atualizaremos as informações no banco de dados.

17. Criar resource para mostrar todos os produtos, no CMD: 

```jsx
php artisan make:resource ProdutoResource
```

18. Definir campos que deseja retornar ao requisitar as informações dos produtos no app\Http\Resources\ProductResource;

```php
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'imagem' => $this->imagem,
            'url' => route('produtos.show', $this->id)
        ];
    }
```

19. Colocar o método do index para retornar todos os produtos: 

```php
    public function index(){
        return ProdutoResource::collection(Produto::all());
    }
```

20. Criar o método para deletar um produto 

```php
    public function destroy(Produto $produto){
        $produto->delete();
        return response()->json("Produto deletado");
    }
```
