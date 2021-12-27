<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RegisterAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $details = [
            'title' => 'Chúc mừng bạn đã tạo tài khoản thành công',
            'body' => 'Yody cảm ơn bạn đã tin tưởng và lựa chọn Yody để đồng hành trong các chặng đường sắp tới. Mọi thắc mắc xin hãy liên hệ đến 19001080 để được giải đáp'
        ];
        \Mail::to($this->data)->send(new \App\Mail\MailConfirmRegister($details));
    }
}
