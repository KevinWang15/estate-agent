<?php

namespace App\Console\Commands;

use App;
use Faker\Generator;
use Illuminate\Console\Command;

class SeedRelations extends Command
{

    private function clearTable($tableName)
    {
        $this->comment("Clearing $tableName");
        \DB::statement("delete from $tableName");
        \DB::statement("alter table $tableName AUTO_INCREMENT=1;");
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:relations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed relations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \DB::statement("SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;");
        $this->clearTable("agent_estate");
        $this->clearTable("orders");
        $this->clearTable("proposals");
        \DB::statement("SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;");

        /** @var Generator $faker */
        $faker = App::make(Generator::class);

        $buyers = App\User::where('user_type', 0)->get();
        $sellers = App\User::where('user_type', 1)->get();
        $agents = App\User::where('user_type', 2)->get();

        $this->comment("Mocking estate_agent relations");
        $estates_id = App\Estate::where('verified', 1)->pluck("id");
        $agents_array = $agents->toArray();
        foreach ($estates_id as $estate_id) {
            shuffle($agents_array);
            $builder = [];
            for ($i = 0; $i < 5; $i++) {
                $builder[] = "({$agents_array[$i]["id"]},$estate_id)";
            }
            \DB::insert("insert into agent_estate (agent_id,estate_id) values " . implode(',', $builder));
        }

        $this->comment("Mocking proposals");
        $proposals = [];
        for ($i = 0; $i < 400; $i++) {
            $proposal = new App\Proposal;
            $proposal->estate_id = App\Helpers\Util::randomArrayMember($estates_id);
            $agents_id = \DB::select("select agent_id from agent_estate where estate_id=?", [$proposal->estate_id]);
            $proposal->agent_id = App\Helpers\Util::randomArrayMember($agents_id)->agent_id;
            $proposal->buyer_id = App\Helpers\Util::randomArrayMember($buyers)->id;
            $proposal->state = $faker->numberBetween(-3, 3);
            $proposal->save();
            $proposals[] = $proposal;
        }

        shuffle($proposals);
        $this->comment("Mocking orders");
        for ($i = 0; $i < 100; $i++) {
            $estate_instance = App\Estate::find($proposals[$i]->estate_id);
            $estate_instance->is_hidden = true;
            $estate_instance->save();

            $order = new \App\Order;
            $order->proposal_id = $proposals[$i]->id;
            $order->state = $faker->numberBetween(0, 2);
            $order->estate_id = $proposals[$i]->estate_id;
            $order->buyer_id = $proposals[$i]->buyer_id;
            $order->seller_id = $estate_instance->user_id;
            $order->save();

            $proposals[$i]->state = 4;
            $proposals[$i]->order_id = $order->id;
            $proposals[$i]->save();
        }
        return;
    }
}
