<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapelDiampu extends Model
{
    protected $table = 'mapel_diampu';
    protected $primaryKey = 'id_ampu';
    public $timestamps = false;

    protected $fillable = [
        'id_guru',
        'id_mapel',
        'id_kelas'
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}
