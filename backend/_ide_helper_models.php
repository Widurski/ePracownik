<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $data_pracy
 * @property numeric $liczba_godzin
 * @property int $dodane_przez
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $dodajacy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Komentarz> $komentarze
 * @property-read int|null $komentarze_count
 * @property-read \App\Models\User $pracownik
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GodzinaPracy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GodzinaPracy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GodzinaPracy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GodzinaPracy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GodzinaPracy whereDataPracy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GodzinaPracy whereDodanePrzez($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GodzinaPracy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GodzinaPracy whereLiczbaGodzin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GodzinaPracy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GodzinaPracy whereUserId($value)
 */
	class GodzinaPracy extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $godzina_pracy_id
 * @property int $user_id
 * @property string $tresc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $autor
 * @property-read \App\Models\GodzinaPracy $godzinaPracy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Komentarz newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Komentarz newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Komentarz query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Komentarz whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Komentarz whereGodzinaPracyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Komentarz whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Komentarz whereTresc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Komentarz whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Komentarz whereUserId($value)
 */
	class Komentarz extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nazwa
 * @property string|null $opis
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereNazwa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereOpis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $imie
 * @property string $nazwisko
 * @property string|null $telefon
 * @property int $role_id
 * @property bool $is_active
 * @property string|null $activation_token
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GodzinaPracy> $dodaneGodziny
 * @property-read int|null $dodane_godziny_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GodzinaPracy> $godzinyPracy
 * @property-read int|null $godziny_pracy_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Komentarz> $komentarze
 * @property-read int|null $komentarze_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereActivationToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereImie($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNazwisko($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelefon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

