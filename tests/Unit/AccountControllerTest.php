<?php

use Tests\TestCase;
use App\Models\Account;

class AccountControllerTest extends TestCase
{
    protected $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->account = Account::factory()->create();
    }

    /**
     * Teste para verificar o saldo de uma conta existente.
     *
     * @return void
     */
    public function testSaldoContaExistente()
    {
        $account = Account::factory()->create();

        $response = $this->json('GET', '/api/contas/' . $account->number . '/saldo');

        $response->assertStatus(200)
            ->assertJsonStructure(['conta', 'saldo'])
            ->assertJson([
                'conta' => $account->number,
                'saldo' => $account->balance,
            ]);
    }

    /**
     * Teste para verificar o saldo de uma conta inexistente.
     *
     * @return void
     */
    public function testSaldoContaInexistente()
    {
        $response = $this->json('GET', '/api/contas/999/saldo');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Conta não encontrada',
            ]);
    }

    /**
     * Teste para verificar o saque de uma conta existente com saldo suficiente.
     *
     * @return void
     */
    public function testSaqueContaExistenteSaldoSuficiente()
    {
        // Inicia uma transação
        DB::beginTransaction();

        // Cria uma conta com saldo de R$ 1000,00
        $account = Account::factory()->create(['balance' => 1000]);

        $response = $this->json('POST', '/api/contas/' . $account->number . '/sacar/500');

        $response->assertStatus(200)
            ->assertJson([
                'saldo' => 500,
            ]);

        $this->assertDatabaseHas('accounts', [
            'number' => $account->number,
            'balance' => 500,
        ]);

        // Desfaz as alterações no banco de dados
        DB::rollBack();
    }

    /**
     * Teste para verificar o saque de uma conta existente com saldo insuficiente.
     *
     * @return void
     */
    public function testSaqueContaExistenteSaldoInsuficiente()
    {
        // Inicia uma transação
        DB::beginTransaction();

        // Cria uma conta com saldo de R$ 1000,00
        $account = Account::factory()->create(['balance' => 1000]);

        // Chama a rota para sacar R$ 1001,00 da conta criada
        $response = $this->json('POST', '/api/contas/' . $account->number . '/sacar/1001');

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Saldo insuficiente',
            ]);

        $this->assertDatabaseHas('accounts', [
            'number' => $account->number,
            'balance' => 1000,
        ]);

        // Desfaz as alterações no banco de dados
        DB::rollBack();
    }

    /**
     * Teste para verificar o saque de uma conta inexistente.
     *
     * @return void
     */
    public function testDepositoContaExistente()
    {
        // Inicia uma transação
        DB::beginTransaction();

        // Cria uma conta
        $account = Account::factory()->create();

        // Chama a rota para depositar R$ 100,00 na conta criada
        $response = $this->post("/api/contas/{$account->number}/depositar/100");

        // Verifica se a resposta tem o status HTTP 200 OK
        $response->assertStatus(200);

        // Verifica se a resposta contém os dados da conta com o saldo atualizado
        $response->assertJson([
            'conta' => $account->number,
            'saldo' => $account->balance + 100
        ]);

        // Desfaz as alterações no banco de dados
        DB::rollBack();
    }

    /**
     * Teste para verificar o depósito em uma conta inexistente.
     *
     * @return void
     */
    public function testDepositoContaInexistente()
    {
        // Chama a rota para depositar R$ 100,00 em uma conta que não existe
        $response = $this->post("/api/contas/99999/depositar/100");

        // Verifica se a resposta tem o status HTTP 404 Not Found
        $response->assertStatus(404);

        // Verifica se a resposta contém a mensagem de erro
        $response->assertJson(['error' => 'Conta não encontrada']);
    }

    /**
     * Teste para verificar o saque de uma conta existente com saldo suficiente.
     *
     * @return void
     */
    public function testSaqueContaExistenteComSaldoSuficiente()
    {
        // Inicia uma transação
        DB::beginTransaction();

        // Cria uma conta com saldo de R$ 500,00
        $account = Account::factory()->create(['balance' => 500]);

        // Chama a rota para sacar R$ 100,00 da conta criada
        $response = $this->post("/api/contas/{$account->number}/sacar/100");

        // Verifica se a resposta tem o status HTTP 200 OK
        $response->assertStatus(200);

        // Verifica se a resposta contém os dados da conta com o saldo atualizado
        $response->assertJson([
            'saldo' => $account->balance - 100
        ]);

        // Desfaz as alterações no banco de dados
        DB::rollBack();
    }

    /**
     * Teste para verificar o saque de uma conta existente com saldo insuficiente.
     *
     * @return void
     */
    public function testSaqueContaExistenteComSaldoInsuficiente()
    {
        // Cria uma conta com saldo de R$ 50,00
        $account = Account::factory()->create(['balance' => 50]);

        // Chama a rota para sacar R$ 100,00 da conta criada
        $response = $this->post("/api/contas/{$account->number}/sacar/100");

        // Verifica se a resposta tem o status HTTP 400 Bad Request
        $response->assertStatus(400);

        // Verifica se a resposta contém a mensagem de erro
        $response->assertJson(['error' => 'Saldo insuficiente']);
    }

    /**
     * Teste para verificar o saque de uma conta inexistente.
     *
     * @return void
     */
    public function testSaqueContaInexistente()
    {
        // Chama a rota para sacar R$ 100,00 de uma conta que não existe
        $response = $this->post("/api/contas/99999/sacar/100");

        // Verifica se a resposta tem o status HTTP 404 Not Found
        $response->assertStatus(404);

        // Verifica se a resposta contém a mensagem de erro
        $response->assertJson(['error' => 'Conta não encontrada']);
    }

    /**
     * Teste para verificar a listagem das contas existentes no banco de dados.
     *
     * @return void
     */
    public function testListarContas()
    {
        // Inicia uma transação
        DB::beginTransaction();

        // Limpa o banco de dados
        DB::table('accounts')->delete();

        // Cria 10 contas
        Account::factory()->count(10)->create();

        // Chama a rota para listar as contas
        $response = $this->get('/api/contas');

        // Verifica se a resposta tem o status HTTP 200 OK
        $response->assertStatus(200);

        // Verifica se a resposta contém os dados das contas criadas
        $response->assertJsonStructure([
            '*' => ['number', 'balance']
        ]);

        // Verifica se a resposta contém 10 contas
        $response->assertJsonCount(10);

        // Desfaz as alterações no banco de dados
        DB::rollBack();
    }

}
