@extends('layouts.child')
<x-app-layout>

    <div class="max-w-3xl mx-auto p-4">

        <h1 class="text-2xl font-bold mb-6 text-orange-700">子どもダッシュボード</h1>

        {{-- 現在のポイント --}}
        <div class="bg-orange-50 shadow rounded p-4 mb-6">
            <p class="text-lg">
                現在のポイント：
                <span class="font-bold text-orange-600">{{ Auth::user()->points }} pt</span>
            </p>
        </div>

        {{-- メニュー --}}
        <div class="bg-orange-50 shadow rounded p-4">
            <h2 class="text-lg font-bold mb-3 text-orange-700">メニュー</h2>

            <ul class="space-y-3">
                <li>
                    <a href="{{ route('child.chores.index') }}" class="text-orange-600 underline"> 
                        お手伝い一覧を見る
                    </a>
                </li>
                <li>
                    <a href="{{ route('child.rewards.index') }}" class="text-orange-600 underline">
                        ご褒美一覧を見る
                    </a>
                </li>
            </ul>
        </div>

    </div>
</x-app-layout>